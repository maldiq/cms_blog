<?php

namespace App\Domain\Page\Observers;

use App\Domain\Page\Models\Page;
use App\Domain\Seo\Http\Controllers\SitemapController;

class PageObserver
{
    public function saving(Page $page): void
    {
        if ($page->isDirty('status')
            && $page->status === Page::STATUS_PUBLISHED
            && $page->published_at === null) {
            $page->published_at = now();
        }
    }

    public function saved(Page $page): void
    {
        if ($page->is_homepage) {
            Page::query()
                ->where('id', '!=', $page->id)
                ->update(['is_homepage' => false]);
        }

        SitemapController::forgetCache();
    }

    public function deleted(Page $page): void
    {
        SitemapController::forgetCache();
    }
}
