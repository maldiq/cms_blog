<?php

namespace App\Domain\Gallery\Album\Services;

use App\Domain\Gallery\Album\Models\Album;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class AlbumService
{
    /**
     * @return LengthAwarePaginator<Album>
     */
    public function getActivePaginated(string $locale, int $perPage = 12): LengthAwarePaginator
    {
        return Album::query()
            ->where('is_active', true)
            ->with([
                'cover',
                'translations' => fn ($query) => $query->where('locale', $locale),
            ])
            ->withCount('media')
            ->orderBy('sort_order')
            ->paginate($perPage);
    }

    /**
     * @return Collection<int, Album>
     */
    public function getActiveList(string $locale): Collection
    {
        return Album::query()
            ->where('is_active', true)
            ->with([
                'cover',
                'translations' => fn ($query) => $query->where('locale', $locale),
            ])
            ->withCount('media')
            ->orderBy('sort_order')
            ->get();
    }

    public function getBySlug(string $slug, string $locale): ?Album
    {
        return Album::query()
            ->where('is_active', true)
            ->whereHas('translations', fn ($query) => $query->where('locale', $locale)->where('slug', $slug))
            ->with([
                'cover',
                'translations',
                'media' => fn ($query) => $query->orderByPivot('sort_order'),
            ])
            ->first();
    }
}
