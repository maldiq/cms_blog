<?php

namespace Tests\Feature\Theme;

use App\Domain\Theme\Filament\Pages\ThemeEditor;
use App\Domain\Theme\Models\Theme;
use App\Domain\Theme\Models\ThemeSetting;
use App\Domain\User\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Support\SeedsServiceProTheme;
use Tests\TestCase;

class ThemeSettingsPersistTest extends TestCase
{
    use RefreshDatabase;
    use SeedsServiceProTheme;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedServiceProEnvironment();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_theme_settings_persist_after_save(): void
    {
        $theme = Theme::query()->where('slug', 'servicepro')->firstOrFail();

        $admin = User::query()->where('email', 'admin@admin.com')->firstOrFail();
        $this->actingAs($admin);

        Livewire::test(ThemeEditor::class)
            ->fillForm([
                'hero' => [
                    'title' => [
                        'id' => 'Judul Hero Tersimpan',
                        'en' => 'Saved Hero Title',
                    ],
                    'subtitle' => [
                        'id' => 'Subjudul tersimpan',
                        'en' => 'Saved subtitle',
                    ],
                    'cta_label' => [
                        'id' => 'Mulai Sekarang',
                        'en' => 'Start Now',
                    ],
                    'cta_url' => 'https://servicepro.demo/id/contact',
                    'background_image_id' => null,
                    'features' => [],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(
            ['id' => 'Judul Hero Tersimpan', 'en' => 'Saved Hero Title'],
            ThemeSetting::get($theme->id, 'hero', 'title')
        );

        $this->get('/id')->assertSee('Judul Hero Tersimpan', false);
        $this->get('/en')->assertSee('Saved Hero Title', false);
    }
}
