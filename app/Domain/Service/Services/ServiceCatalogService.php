<?php

namespace App\Domain\Service\Services;

use App\Domain\Service\Models\Service;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ServiceCatalogService
{
    /**
     * @return Collection<int, Service>
     */
    /**
     * Layanan aktif yang punya teks untuk ditampilkan di menu header.
     *
     * @return Collection<int, Service>
     */
    public function listForHeaderMenu(): Collection
    {
        return Service::query()
            ->active()
            ->ordered()
            ->with('translations')
            ->get()
            ->filter(function (Service $service): bool {
                $translation = $service->translate(app()->getLocale(), false)
                    ?: $service->translate('id', false);

                if ($translation === null) {
                    return false;
                }

                $content = trim(strip_tags((string) $translation->content));
                $excerpt = trim((string) $translation->excerpt);

                return $content !== '' || $excerpt !== '';
            })
            ->values();
    }

    public function listActiveForHome(int $limit = 6): Collection
    {
        return Service::query()
            ->with(['translations', 'cover'])
            ->active()
            ->ordered()
            ->limit($limit)
            ->get();
    }

    public function findBySlug(string $slug, string $locale): ?Service
    {
        return Service::query()
            ->whereHas('translations', function ($query) use ($slug, $locale): void {
                $query->where('locale', $locale)->where('slug', $slug);
            })
            ->with(['translations', 'cover'])
            ->active()
            ->first();
    }

    /**
     * @return LengthAwarePaginator<int, Service>
     */
    public function paginateActive(int $perPage = 12): LengthAwarePaginator
    {
        return Service::query()
            ->with(['translations', 'cover'])
            ->active()
            ->ordered()
            ->paginate($perPage);
    }

    /**
     * @return Collection<int, Service>
     */
    public function relatedServices(Service $service, int $limit = 3): Collection
    {
        return Service::query()
            ->with(['translations', 'cover'])
            ->active()
            ->ordered()
            ->where('id', '!=', $service->id)
            ->limit($limit)
            ->get();
    }
}
