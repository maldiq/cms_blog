<?php

namespace App\Domain\Page\Services;

use App\Domain\Page\Models\Page;

class PageService
{
    public function getBySlug(string $slug, string $locale): ?Page
    {
        return Page::query()
            ->published()
            ->whereHas('translations', fn ($query) => $query->where('locale', $locale)->where('slug', $slug))
            ->with(['translations', 'user'])
            ->first();
    }

    public function getHomepage(string $locale): ?Page
    {
        return Page::query()
            ->published()
            ->where('is_homepage', true)
            ->with(['translations', 'user'])
            ->first();
    }
}
