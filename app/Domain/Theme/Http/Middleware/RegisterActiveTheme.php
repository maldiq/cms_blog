<?php

namespace App\Domain\Theme\Http\Middleware;

use App\Domain\Theme\Models\Theme;
use App\Domain\Theme\Services\ThemeService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class RegisterActiveTheme
{
    public function __construct(
        private readonly ThemeService $themeService,
    ) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $theme = Theme::current();

        View::share('activeTheme', $theme);

        if ($theme !== null) {
            $this->themeService->registerViewNamespaceForSlug($theme->slug);
        }

        return $next($request);
    }
}
