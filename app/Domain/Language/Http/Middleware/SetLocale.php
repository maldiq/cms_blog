<?php

namespace App\Domain\Language\Http\Middleware;

use App\Domain\Language\Models\Language;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Tentukan locale dari URL → session → bahasa default.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldSkip($request)) {
            return $next($request);
        }

        $localeFromUrl = $request->route('locale') ?? $request->segment(1);

        if (! Schema::hasTable('languages')) {
            app()->setLocale(config('app.locale', 'id'));

            return $next($request);
        }

        if (filled($localeFromUrl)) {
            $language = Language::query()->where('code', $localeFromUrl)->first();

            if ($language === null) {
                abort(404);
            }

            if (! $language->is_active) {
                abort(404);
            }

            app()->setLocale($language->code);
            session(['locale' => $language->code]);

            return $next($request);
        }

        $sessionLocale = session('locale');

        if (
            filled($sessionLocale)
            && Language::query()->active()->where('code', $sessionLocale)->exists()
        ) {
            app()->setLocale($sessionLocale);

            return $next($request);
        }

        $default = Language::getDefault();
        app()->setLocale($default?->code ?? config('app.locale', 'id'));

        return $next($request);
    }

    private function shouldSkip(Request $request): bool
    {
        return $request->is(
            'kelola',
            'kelola/*',
            'admin',
            'admin/*',
            'up',
            'livewire/*',
            'build/*',
            'storage/*',
            'js/*',
            'css/*',
            'favicon.ico',
        );
    }
}
