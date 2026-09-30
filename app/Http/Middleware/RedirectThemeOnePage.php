<?php

namespace App\Http\Middleware;

use App\Support\Theme\ThemeOnePage;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectThemeOnePage
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! ThemeOnePage::isActive()) {
            return $next($request);
        }

        if ($request->routeIs('home')) {
            return $next($request);
        }

        $routeName = $request->route()?->getName();

        if ($routeName === 'blog.show') {
            return $next($request);
        }

        $locale = (string) $request->route('locale');
        $map = ThemeOnePage::routeFragmentMap();
        $fragment = $map[$routeName] ?? 'top';

        return redirect()->to(ThemeOnePage::homeUrl($locale, $fragment));
    }
}
