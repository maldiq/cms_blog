<?php

namespace App\Domain\Theme\Services;

use App\Domain\Theme\Models\Theme;
use App\Domain\Theme\Models\ThemeSetting;
use Illuminate\Support\Facades\View;

class ThemeService
{
    public function getCurrentTheme(): ?Theme
    {
        return Theme::current();
    }

    public function getSetting(string $key, mixed $default = null): mixed
    {
        $theme = $this->getCurrentTheme();

        if ($theme === null) {
            return $default;
        }

        $parts = explode('.', $key, 2);

        if (count($parts) < 2) {
            return $default;
        }

        [$group, $settingKey] = $parts;

        return ThemeSetting::get($theme->id, $group, $settingKey, $default);
    }

    /**
     * @return array<string, mixed>
     */
    public function getGroup(string $group): array
    {
        $theme = $this->getCurrentTheme();

        if ($theme === null) {
            return [];
        }

        return ThemeSetting::getGroup($theme->id, $group);
    }

    public function clearCache(): void
    {
        Theme::clearCache();
    }

    public function applyTheme(): void
    {
        $theme = $this->getCurrentTheme();

        if ($theme === null) {
            return;
        }

        $path = resource_path('themes/' . $theme->slug);

        if (! is_dir($path)) {
            return;
        }

        View::addNamespace('theme', $path);
    }

    public function viewName(string $view): string
    {
        $slug = $this->getCurrentTheme()?->slug ?? 'default';

        return 'themes.' . $slug . '.' . $view;
    }

    public function assetUrl(string $path): string
    {
        $slug = $this->getCurrentTheme()?->slug ?? 'default';
        $normalized = ltrim(str_replace('\\', '/', $path), '/');

        return asset('themes/' . $slug . '/' . $normalized);
    }
}
