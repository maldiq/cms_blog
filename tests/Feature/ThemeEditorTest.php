<?php

namespace Tests\Feature;

use App\Domain\Theme\Filament\Pages\ThemeEditor;
use App\Domain\Theme\Models\Theme;
use App\Domain\Theme\Models\ThemeSetting;
use App\Domain\User\Models\User;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ThemeEditorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            LanguageSeeder::class,
            RolePermissionSeeder::class,
        ]);

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    protected function createActiveTheme(): Theme
    {
        return Theme::query()->create([
            'name' => 'ServicePro',
            'slug' => 'servicepro',
            'is_active' => true,
        ]);
    }

    protected function actingAsAdmin(): User
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');
        $this->actingAs($admin);

        return $admin;
    }

    public function test_theme_editor_loads_current_settings(): void
    {
        $theme = $this->createActiveTheme();

        ThemeSetting::set($theme->id, 'hero', 'title', [
            'id' => 'Judul tersimpan',
            'en' => 'Saved title',
        ]);

        $this->actingAsAdmin();

        Livewire::test(ThemeEditor::class)
            ->assertFormSet([
                'hero' => [
                    'title' => [
                        'id' => 'Judul tersimpan',
                        'en' => 'Saved title',
                    ],
                ],
            ]);
    }

    public function test_can_save_hero_settings(): void
    {
        $theme = $this->createActiveTheme();
        $this->actingAsAdmin();

        Livewire::test(ThemeEditor::class)
            ->fillForm([
                'hero' => [
                    'title' => [
                        'id' => 'Hero ID',
                        'en' => 'Hero EN',
                    ],
                    'subtitle' => [
                        'id' => 'Sub ID',
                        'en' => 'Sub EN',
                    ],
                    'cta_label' => [
                        'id' => 'Mulai',
                        'en' => 'Start',
                    ],
                    'cta_url' => 'https://example.com',
                    'background_image_id' => null,
                    'features' => [],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(
            ['id' => 'Hero ID', 'en' => 'Hero EN'],
            ThemeSetting::get($theme->id, 'hero', 'title')
        );

        $this->assertSame('https://example.com', ThemeSetting::get($theme->id, 'hero', 'cta_url'));
    }

    public function test_can_save_branding_settings(): void
    {
        $theme = $this->createActiveTheme();
        $this->actingAsAdmin();

        Livewire::test(ThemeEditor::class)
            ->fillForm([
                'branding' => [
                    'logo_id' => null,
                    'logo_dark_id' => null,
                    'favicon_id' => null,
                    'primary_color' => '#112233',
                    'secondary_color' => '#445566',
                    'accent_color' => '#778899',
                    'font_heading' => 'Montserrat',
                    'font_body' => 'Inter',
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('#112233', ThemeSetting::get($theme->id, 'branding', 'primary_color'));
        $this->assertSame('Montserrat', ThemeSetting::get($theme->id, 'branding', 'font_heading'));
    }

    public function test_reset_to_default_removes_settings(): void
    {
        $theme = $this->createActiveTheme();

        ThemeSetting::set($theme->id, 'hero', 'title', [
            'id' => 'Custom',
            'en' => 'Custom EN',
        ]);

        ThemeSetting::set($theme->id, 'contact', 'email', 'custom@example.com');

        $this->actingAsAdmin();

        Livewire::test(ThemeEditor::class)
            ->call('resetToDefault')
            ->assertHasNoFormErrors();

        $this->assertSame(
            'hello@servicepro.demo',
            ThemeSetting::get($theme->id, 'contact', 'email')
        );

        $this->assertSame(
            ['id' => 'Solusi Profesional untuk Bisnis Anda', 'en' => 'Professional Solutions for Your Business'],
            ThemeSetting::get($theme->id, 'hero', 'title')
        );

        $this->assertSame('#0f766e', ThemeSetting::get($theme->id, 'branding', 'primary_color'));
    }
}
