<?php

namespace Tests\Feature;

use App\Domain\Menu\Models\Menu;
use App\Domain\Menu\Models\MenuItem;
use App\Domain\Setting\Settings\GeneralSettings;
use App\Domain\Setting\Settings\SocialSettings;
use App\Domain\Theme\Models\Theme;
use App\Domain\Theme\Models\ThemeSetting;
use App\Domain\Theme\Providers\ThemeServiceProvider;
use Database\Seeders\LanguageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ServiceProLayoutTest extends TestCase
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

    public function test_header_renders_menu_from_database(): void
    {
        $this->activateServiceProTheme();

        $general = app(GeneralSettings::class);
        $general->site_name = 'CMS Blog Demo';
        $general->save();

        $menu = Menu::factory()->create([
            'location' => 'header',
            'is_active' => true,
        ]);

        MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'label' => ['id' => 'Layanan Kami', 'en' => 'Our Services'],
            'url' => '/id/layanan',
            'sort_order' => 1,
        ]);

        $this->get('/id')
            ->assertOk()
            ->assertSee('Layanan Kami', false);
    }

    public function test_footer_renders_columns_from_database(): void
    {
        $theme = $this->activateServiceProTheme();

        ThemeSetting::set($theme->id, 'footer', 'columns', [
            [
                'title' => ['id' => 'Tautan Cepat', 'en' => 'Quick Links'],
                'menu_location' => 'footer-links',
            ],
        ]);

        ThemeSetting::set($theme->id, 'footer', 'about_text', [
            'id' => 'Teks tentang perusahaan.',
            'en' => 'About company text.',
        ]);

        $menu = Menu::factory()->create([
            'location' => 'footer-links',
            'is_active' => true,
        ]);

        MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'label' => ['id' => 'Kebijakan Privasi', 'en' => 'Privacy Policy'],
            'url' => '/id/privacy',
            'sort_order' => 1,
        ]);

        app(SocialSettings::class)->fill(['instagram' => 'https://instagram.com/demo'])->save();

        $this->get('/id')
            ->assertOk()
            ->assertSee('Tautan Cepat', false)
            ->assertSee('Kebijakan Privasi', false)
            ->assertSee('Teks tentang perusahaan.', false)
            ->assertSee('https://instagram.com/demo', false);
    }

    public function test_layout_does_not_error_when_setting_empty(): void
    {
        $this->activateServiceProTheme();

        $this->get('/id')->assertOk();
    }

    public function test_back_to_top_button_renders(): void
    {
        $this->activateServiceProTheme();

        $this->get('/id')
            ->assertOk()
            ->assertSee(__('messages.back_to_top'), false);
    }
}
