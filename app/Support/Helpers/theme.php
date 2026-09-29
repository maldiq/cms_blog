<?php

use App\Domain\Theme\Services\ThemeService;

if (! function_exists('theme')) {
    /**
     * Ambil setting theme aktif (format key: group.key).
     */
    function theme(string $key, mixed $default = null): mixed
    {
        return app(ThemeService::class)->getSetting($key, $default);
    }
}

if (! function_exists('theme_group')) {
    /**
     * @return array<string, mixed>
     */
    function theme_group(string $group): array
    {
        return app(ThemeService::class)->getGroup($group);
    }
}

if (! function_exists('theme_asset')) {
    function theme_asset(string $path): string
    {
        return app(ThemeService::class)->assetUrl($path);
    }
}

if (! function_exists('theme_view')) {
    function theme_view(string $view): string
    {
        return app(ThemeService::class)->viewName($view);
    }
}
