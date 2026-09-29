<?php

namespace App\Support\Theme;

use App\Domain\Language\Models\Language;
use Illuminate\Support\Facades\Cache;

class HomeSectionCache
{
    public const TTL_SECONDS = 3600;

    public static function key(string $section, ?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return "home.{$section}.{$locale}";
    }

    public static function forgetAll(): void
    {
        foreach (Language::getActive() as $language) {
            foreach (['stats', 'pricing', 'faq'] as $section) {
                Cache::forget(self::key($section, $language->code));
            }
        }
    }
}
