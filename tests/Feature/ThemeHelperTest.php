<?php

namespace Tests\Feature;

use App\Domain\Theme\Models\Theme;
use App\Domain\Theme\Models\ThemeSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ThemeHelperTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Theme::clearCache();
        Cache::flush();
    }

    private function seedActiveTheme(string $slug = 'servicepro'): Theme
    {
        return Theme::query()->create([
            'name' => 'Service Pro',
            'slug' => $slug,
            'is_active' => true,
        ]);
    }

    public function test_theme_helper_returns_value(): void
    {
        $theme = $this->seedActiveTheme();

        ThemeSetting::set($theme->id, 'hero', 'title', ['id' => 'Selamat datang']);

        $this->assertSame(['id' => 'Selamat datang'], theme('hero.title'));
    }

    public function test_theme_helper_returns_default_if_missing(): void
    {
        $this->seedActiveTheme();

        $this->assertSame('default-value', theme('hero.missing', 'default-value'));
    }

    public function test_theme_group_returns_all_keys(): void
    {
        $theme = $this->seedActiveTheme();

        ThemeSetting::set($theme->id, 'hero', 'title', ['id' => 'Judul']);
        ThemeSetting::set($theme->id, 'hero', 'subtitle', ['id' => 'Sub']);

        $group = theme_group('hero');

        $this->assertArrayHasKey('title', $group);
        $this->assertArrayHasKey('subtitle', $group);
        $this->assertSame(['id' => 'Judul'], $group['title']);
    }

    public function test_theme_view_returns_correct_path(): void
    {
        $this->seedActiveTheme('servicepro');

        $this->assertSame('themes.servicepro.layouts.app', theme_view('layouts.app'));
    }

    public function test_theme_asset_returns_correct_url(): void
    {
        $this->seedActiveTheme('servicepro');

        $url = theme_asset('css/theme.css');

        $this->assertStringContainsString('/themes/servicepro/css/theme.css', $url);
    }

    public function test_theme_copyright_line_replaces_year_and_site_name(): void
    {
        $theme = $this->seedActiveTheme('servicepro');

        ThemeSetting::set($theme->id, 'footer', 'copyright', [
            'id' => 'Copyright © :year :site_name. Semua hak dilindungi.',
            'en' => 'Copyright © :year :site_name. All Rights Reserved',
        ]);

        app()->setLocale('en');

        $line = theme_copyright_line();

        $this->assertIsString($line);
        $this->assertStringContainsString((string) date('Y'), $line);
        $this->assertStringNotContainsString(':year', $line);
        $this->assertStringContainsString('All Rights Reserved', $line);
    }
}
