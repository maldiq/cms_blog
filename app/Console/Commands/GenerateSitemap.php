<?php

namespace App\Console\Commands;

use App\Domain\Seo\Http\Controllers\SitemapController;
use App\Domain\Seo\Services\SitemapService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Regenerate cache sitemap.xml untuk seluruh URL publik';

    public function handle(SitemapService $sitemapService): int
    {
        SitemapController::forgetCache();

        $xml = $sitemapService->buildXml();
        Cache::put('seo.sitemap.xml', $xml, 86400);

        $path = storage_path('app/sitemap.xml');
        file_put_contents($path, $xml);

        $this->info("Sitemap di-cache dan disalin ke {$path}");

        return self::SUCCESS;
    }
}
