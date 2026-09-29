<?php

namespace App\Domain\Service\Observers;

use App\Domain\Menu\Services\HeaderMenuFromContentService;
use App\Domain\Seo\Http\Controllers\SitemapController;
use App\Domain\Service\Models\Service;

class ServiceObserver
{
    public function saved(Service $service): void
    {
        app(HeaderMenuFromContentService::class)->sync();
        SitemapController::forgetCache();
    }

    public function deleted(Service $service): void
    {
        app(HeaderMenuFromContentService::class)->sync();
        SitemapController::forgetCache();
    }
}
