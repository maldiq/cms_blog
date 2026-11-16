<?php

namespace App\Domain\Affiliate\Http\Middleware;

use App\Domain\Affiliate\Services\TrafficSource;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CaptureTrafficSource
{
    public function __construct(
        private readonly TrafficSource $trafficSource,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->shouldSkip($request)) {
            $this->trafficSource->capture($request);
        }

        return $next($request);
    }

    private function shouldSkip(Request $request): bool
    {
        if ($request->is(
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
            'sitemap.xml',
            'robots.txt',
        )) {
            return true;
        }

        return $request->segment(2) === 'livewire';
    }
}
