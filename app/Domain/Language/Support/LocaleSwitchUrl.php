<?php

namespace App\Domain\Language\Support;

use App\Domain\Language\Models\Language;
use Illuminate\Http\Request;

class LocaleSwitchUrl
{
    /**
     * URL halaman yang sama dengan locale diganti (untuk switcher header).
     */
    public static function forLocale(string $targetLocale, ?Request $request = null): string
    {
        $request ??= request();

        $segments = $request->segments();

        if (
            isset($segments[0])
            && Language::query()->where('code', $segments[0])->exists()
        ) {
            $segments[0] = $targetLocale;
        } else {
            array_unshift($segments, $targetLocale);
        }

        $path = '/'.implode('/', $segments);

        $query = $request->getQueryString();

        return $query !== null && $query !== '' ? $path.'?'.$query : $path;
    }
}
