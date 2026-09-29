<?php

namespace App\Domain\Portfolio\Services;

use App\Domain\Portfolio\Models\Portfolio;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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

    /**
     * @return LengthAwarePaginator<int, Portfolio>
     */
    public function paginateActive(int $perPage = 12): LengthAwarePaginator
    {
        return Portfolio::query()
            ->with(['translations', 'cover', 'category.translations'])
            ->active()
            ->ordered()
            ->paginate($perPage);
    }

    /**
     * @return Collection<int, Portfolio>
     */
    public function relatedPortfolios(Portfolio $portfolio, int $limit = 3): Collection
    {
        return Portfolio::query()
            ->with(['translations', 'cover', 'category.translations'])
            ->active()
            ->ordered()
            ->when($portfolio->category_id, fn ($query) => $query->where('category_id', $portfolio->category_id))
            ->where('id', '!=', $portfolio->id)
            ->limit($limit)
            ->get();
    }
}
