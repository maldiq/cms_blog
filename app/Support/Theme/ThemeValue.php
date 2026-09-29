<?php

namespace App\Support\Theme;

class ThemeValue
{
    /**
     * Resolve nilai translatable theme (array locale => string) ke locale aktif.
     */
    public static function localize(mixed $value, ?string $locale = null): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        if ($value === []) {
            return null;
        }

        if (! self::isTranslatableArray($value)) {
            return $value;
        }

        $locale ??= app()->getLocale();

        if (filled($value[$locale] ?? null)) {
            return $value[$locale];
        }

        foreach (['id', 'en'] as $fallback) {
            if (filled($value[$fallback] ?? null)) {
                return $value[$fallback];
            }
        }

        $first = reset($value);

        return is_string($first) ? $first : $value;
    }

    /**
     * @param  array<string, mixed>  $value
     */
    public static function isTranslatableArray(array $value): bool
    {
        foreach (array_keys($value) as $key) {
            if (! is_string($key) || strlen($key) !== 2) {
                return false;
            }
        }

        return true;
    }
}
