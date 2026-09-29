<?php

namespace Tests\Feature;

use App\Domain\Theme\Filament\Pages\ThemeEditor;
use App\Domain\Theme\Filament\Pages\ThemeSelector;
use App\Domain\Theme\Models\Theme;
use App\Domain\User\Models\User;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ThemeSelectorTest extends TestCase
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

    public function test_theme_selector_lists_registered_themes(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');

        $this->actingAs($admin);

        Livewire::test(ThemeSelector::class)
            ->assertSee('ServicePro')
            ->assertSee('CMS Blog');
    }

    public function test_can_activate_theme(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');

        Theme::query()->create([
            'name' => 'Legacy',
            'slug' => 'legacy',
            'is_active' => true,
        ]);

        $this->actingAs($admin);

        Livewire::test(ThemeSelector::class)
            ->call('activateTheme', 'servicepro')
            ->assertRedirect(ThemeEditor::getUrl());

        $this->assertDatabaseHas('themes', [
            'slug' => 'servicepro',
            'is_active' => 1,
        ]);

        $this->assertDatabaseHas('themes', [
            'slug' => 'legacy',
            'is_active' => 0,
        ]);
    }

    public function test_non_admin_cannot_access_theme_selector(): void
    {
        $editor = User::factory()->create(['is_active' => true]);
        $editor->assignRole('editor');

        $this->actingAs($editor)
            ->get('/kelola/themes')
            ->assertForbidden();
    }
}
