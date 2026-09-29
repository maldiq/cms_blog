<?php

namespace Tests\Feature;

use App\Domain\Theme\Models\Theme;
use App\Domain\Theme\Models\ThemeSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ThemeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Theme::clearCache();
        Cache::flush();
    }

    public function test_can_create_theme(): void
    {
        $theme = Theme::query()->create([
            'name' => 'Default Theme',
            'slug' => 'default',
            'description' => 'Tema bawaan',
            'author' => 'CMS Blog',
            'version' => '1.0.0',
            'is_active' => false,
        ]);

        $this->assertDatabaseHas('themes', [
            'id' => $theme->id,
            'slug' => 'default',
            'is_active' => 0,
        ]);
    }

    public function test_theme_setting_get_returns_value(): void
    {
        $theme = Theme::query()->create([
            'name' => 'Default',
            'slug' => 'default',
            'is_active' => true,
        ]);

        ThemeSetting::set($theme->id, 'hero', 'title', ['id' => 'Selamat datang']);

        $this->assertSame(['id' => 'Selamat datang'], ThemeSetting::get($theme->id, 'hero', 'title'));
    }

    public function test_theme_setting_get_returns_default_if_missing(): void
    {
        $theme = Theme::query()->create([
            'name' => 'Default',
            'slug' => 'default',
            'is_active' => true,
        ]);

        $this->assertSame('fallback', ThemeSetting::get($theme->id, 'hero', 'missing_key', 'fallback'));
    }

    public function test_current_theme_returns_active_theme(): void
    {
        Theme::query()->create([
            'name' => 'Inactive',
            'slug' => 'inactive',
            'is_active' => false,
        ]);

        $active = Theme::query()->create([
            'name' => 'Active Theme',
            'slug' => 'active',
            'is_active' => true,
        ]);

        $this->assertNotNull(Theme::current());
        $this->assertSame($active->id, Theme::current()?->id);
    }
}
