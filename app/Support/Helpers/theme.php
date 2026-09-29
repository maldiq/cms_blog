<?php

use App\Domain\Media\Models\Media;
use App\Domain\Theme\Models\Theme;
use App\Domain\Theme\Services\ThemeService;
use App\Support\Theme\ThemeValue;

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

if (! function_exists('theme_layout')) {
    function theme_layout(): string
    {
        if (Theme::current() === null) {
            return 'layouts.app';
        }

        app(ThemeService::class)->applyTheme();

        return theme_view('layouts.app');
    }
}

if (! function_exists('theme_locale')) {
    /**
     * Ambil setting theme dan resolve locale untuk field translatable.
     */
    function theme_locale(string $key, mixed $default = null): mixed
    {
        return ThemeValue::localize(theme($key, $default));
    }
}

if (! function_exists('media_url')) {
    function media_url(mixed $mediaId): ?string
    {
        if ($mediaId === null || $mediaId === '') {
            return null;
        }

        $media = Media::query()->find((int) $mediaId);

        return $media?->getFullUrl();
    }
}
