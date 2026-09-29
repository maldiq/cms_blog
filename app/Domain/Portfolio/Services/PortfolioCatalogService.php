<?php

namespace App\Domain\Portfolio\Services;

use App\Domain\Portfolio\Models\Portfolio;
use Illuminate\Database\Eloquent\Collection;

class PortfolioCatalogService
{
    /**
     * @return Collection<int, Portfolio>
     */
    public function listFeaturedForHome(int $limit = 6): Collection
    {
        return Portfolio::query()
            ->with(['translations', 'cover', 'category.translations'])
            ->active()
            ->featured()
            ->ordered()
            ->limit($limit)
            ->get();
    }

    /**
     * @return Collection<int, Portfolio>
     */
    public function listActive(int $limit = 100): Collection
    {
        return Portfolio::query()
            ->with(['translations', 'cover', 'category.translations'])
            ->active()
            ->ordered()
            ->limit($limit)
            ->get();
    }

    public function findBySlug(string $slug, string $locale): ?Portfolio
    {
        return Portfolio::query()
            ->whereHas('translations', function ($query) use ($slug, $locale): void {
                $query->where('locale', $locale)->where('slug', $slug);
            })
            ->with(['translations', 'cover', 'category.translations'])
            ->active()
            ->first();
    }
}
