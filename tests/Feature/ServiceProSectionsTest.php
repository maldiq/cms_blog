<?php

namespace Tests\Feature;

use App\Domain\Theme\Models\Theme;
use App\Domain\Theme\Models\ThemeSetting;
use App\Domain\Theme\Providers\ThemeServiceProvider;
use Database\Seeders\LanguageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ServiceProSectionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LanguageSeeder::class);

        Theme::clearCache();
        Cache::flush();
    }

    protected function activateServiceProTheme(): Theme
    {
        $theme = Theme::query()->create([
            'name' => 'ServicePro',
            'slug' => 'servicepro',
            'is_active' => true,
        ]);

        Theme::clearCache();

        /** @var ThemeServiceProvider $provider */
        $provider = $this->app->getProvider(ThemeServiceProvider::class);
        $provider->registerThemeViewNamespace('servicepro');

        return $theme;
    }

    public function test_hero_renders_title_from_setting(): void
    {
        $theme = $this->activateServiceProTheme();

        ThemeSetting::set($theme->id, 'hero', 'title', [
            'id' => 'Judul Hero Utama',
            'en' => 'Main Hero Title',
        ]);

        $this->get('/id')
            ->assertOk()
            ->assertSee('Judul Hero Utama', false);
    }

    public function test_hero_renders_cta(): void
    {
        $theme = $this->activateServiceProTheme();

        ThemeSetting::set($theme->id, 'hero', 'cta_label', [
            'id' => 'Mulai Sekarang',
            'en' => 'Start Now',
        ]);
        ThemeSetting::set($theme->id, 'hero', 'cta_url', 'https://example.com/start');

        $this->get('/id')
            ->assertOk()
            ->assertSee('Mulai Sekarang', false)
            ->assertSee('https://example.com/start', false);
    }

    public function test_stats_renders_items(): void
    {
        $theme = $this->activateServiceProTheme();

        ThemeSetting::set($theme->id, 'stats', 'items', [
            [
                'number' => '120',
                'suffix' => '+',
                'label' => ['id' => 'Proyek Selesai', 'en' => 'Projects Done'],
            ],
        ]);

        $this->get('/id')
            ->assertOk()
            ->assertSee('Proyek Selesai', false)
            ->assertSee('data-count-up', false)
            ->assertSee('data-target="120"', false);
    }

    public function test_cta_renders_button(): void
    {
        $theme = $this->activateServiceProTheme();

        ThemeSetting::set($theme->id, 'cta', 'title', [
            'id' => 'Siap Berkolaborasi',
            'en' => 'Ready to Collaborate',
        ]);
        ThemeSetting::set($theme->id, 'cta', 'cta_label', [
            'id' => 'Hubungi Kami',
            'en' => 'Contact Us',
        ]);
        ThemeSetting::set($theme->id, 'cta', 'cta_url', 'https://example.com/contact');

        $this->get('/id')
            ->assertOk()
            ->assertSee('Siap Berkolaborasi', false)
            ->assertSee('Hubungi Kami', false)
            ->assertSee('https://example.com/contact', false);
    }

    public function test_section_does_not_error_when_setting_empty(): void
    {
        $this->activateServiceProTheme();

        $this->get('/id')
            ->assertOk()
            ->assertSee('theme-hero-fade', false)
            ->assertSee('data-theme-stats', false);
    }
}
