<?php

namespace Tests\Feature;

use App\Domain\Service\Models\Service;
use App\Domain\Theme\Models\Theme;
use App\Domain\Theme\Models\ThemeSetting;
use App\Domain\Theme\Providers\ThemeServiceProvider;
use Database\Seeders\LanguageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ServiceProContentSectionsTest extends TestCase
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

    public function test_services_section_renders_active_services(): void
    {
        $this->activateServiceProTheme();

        $service = Service::factory()->create([
            'icon' => '⚡',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $service->translateOrNew('id')->fill([
            'title' => 'Konsultasi IT',
            'slug' => 'konsultasi-it',
            'excerpt' => 'Layanan konsultasi teknologi.',
        ])->save();

        $this->get('/id')
            ->assertOk()
            ->assertSee('Konsultasi IT', false)
            ->assertSee('Layanan konsultasi teknologi.', false)
            ->assertSee(route('services.show', ['locale' => 'id', 'slug' => 'konsultasi-it']), false);
    }

    public function test_services_section_does_not_render_inactive(): void
    {
        $this->activateServiceProTheme();

        $active = Service::factory()->create(['is_active' => true, 'sort_order' => 1]);
        $active->translateOrNew('id')->fill([
            'title' => 'Layanan Aktif',
            'slug' => 'layanan-aktif',
            'excerpt' => 'Aktif',
        ])->save();

        $inactive = Service::factory()->create(['is_active' => false, 'sort_order' => 2]);
        $inactive->translateOrNew('id')->fill([
            'title' => 'Layanan Nonaktif',
            'slug' => 'layanan-nonaktif',
            'excerpt' => 'Nonaktif',
        ])->save();

        $this->get('/id')
            ->assertOk()
            ->assertSee('Layanan Aktif', false)
            ->assertDontSee('Layanan Nonaktif', false);
    }

    public function test_about_section_renders_points(): void
    {
        $theme = $this->activateServiceProTheme();

        ThemeSetting::set($theme->id, 'about', 'title', [
            'id' => 'Tentang Kami',
            'en' => 'About Us',
        ]);

        ThemeSetting::set($theme->id, 'about', 'points', [
            ['text' => ['id' => 'Tim berpengalaman', 'en' => 'Experienced team']],
            ['text' => ['id' => 'Proses transparan', 'en' => 'Transparent process']],
        ]);

        $this->get('/id')
            ->assertOk()
            ->assertSee('Tentang Kami', false)
            ->assertSee('Tim berpengalaman', false)
            ->assertSee('Proses transparan', false);
    }

    public function test_process_section_renders_steps(): void
    {
        $theme = $this->activateServiceProTheme();

        ThemeSetting::set($theme->id, 'process', 'title', [
            'id' => 'Alur Kerja',
            'en' => 'Workflow',
        ]);

        ThemeSetting::set($theme->id, 'process', 'steps', [
            [
                'number' => '01',
                'title' => ['id' => 'Analisis', 'en' => 'Analysis'],
                'description' => ['id' => 'Kebutuhan klien', 'en' => 'Client needs'],
            ],
        ]);

        $this->get('/id')
            ->assertOk()
            ->assertSee('Alur Kerja', false)
            ->assertSee('01', false)
            ->assertSee('Analisis', false)
            ->assertSee('Kebutuhan klien', false)
            ->assertSee('theme-process-step', false);
    }
}
