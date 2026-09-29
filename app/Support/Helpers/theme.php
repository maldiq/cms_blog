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

if (! function_exists('theme_copyright_line')) {
    /**
     * Teks copyright footer dengan placeholder :year dan :site_name.
     */
    function theme_copyright_line(): ?string
    {
        $text = theme_locale('footer.copyright');

        if (! is_string($text) || trim($text) === '') {
            return null;
        }

        $replacements = [
            ':year' => (string) date('Y'),
            ':site_name' => (string) (setting('site_name') ?? ''),
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $text);
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
