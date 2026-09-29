<?php

namespace App\Domain\Seo\Http\Controllers;

use App\Domain\Seo\Services\SitemapService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController
{
    private const CACHE_KEY = 'seo.sitemap.xml';

    private const CACHE_TTL_SECONDS = 86400;

    public function __invoke(SitemapService $sitemapService): Response
    {
        $xml = Cache::remember(self::CACHE_KEY, self::CACHE_TTL_SECONDS, fn (): string => $sitemapService->buildXml());

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }

    public static function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
