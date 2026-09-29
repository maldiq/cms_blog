<?php

namespace App\Domain\Blog\Post\Services;

use App\Domain\Blog\Category\Models\Category;
use App\Domain\Blog\Post\Models\Post;
use App\Domain\Blog\Series\Models\Series;
use App\Domain\Blog\Tag\Models\Tag;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class PostService
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function getPublished(string $locale, array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $query = Post::query()
            ->published()
            ->with([
                'translations' => fn ($q) => $q->where('locale', $locale),
                'user:id,name',
                'categories.translations' => fn ($q) => $q->where('locale', $locale),
                'featuredImage',
            ])
            ->whereHas('translations', fn (Builder $q) => $q->where('locale', $locale));

        if (filled($filters['category'] ?? null)) {
            $categorySlug = (string) $filters['category'];
            $query->whereHas('categories.translations', function (Builder $q) use ($categorySlug, $locale): void {
                $q->where('locale', $locale)->where('slug', $categorySlug);
            });
        }

        if (filled($filters['tag'] ?? null)) {
            $tagSlug = (string) $filters['tag'];
            $query->whereHas('tags.translations', function (Builder $q) use ($tagSlug, $locale): void {
                $q->where('locale', $locale)->where('slug', $tagSlug);
            });
        }

        if (filled($filters['search'] ?? null)) {
            $search = (string) $filters['search'];
            $query->whereHas('translations', function (Builder $q) use ($search, $locale): void {
                $q->where('locale', $locale)
                    ->where(function (Builder $inner) use ($search): void {
                        $inner->where('title', 'like', "%{$search}%")
                            ->orWhere('excerpt', 'like', "%{$search}%");
                    });
            });
        }

        return $query
            ->latest('published_at')
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getBySlug(string $slug, string $locale): ?Post
    {
        $cacheKey = "post.slug.{$locale}.{$slug}";

        return Cache::remember($cacheKey, now()->addHour(), function () use ($slug, $locale): ?Post {
            return Post::query()
                ->published()
                ->whereHas('translations', fn (Builder $q) => $q->where('locale', $locale)->where('slug', $slug))
                ->with([
                    'translations' => fn ($q) => $q->where('locale', $locale),
                    'user:id,name,email',
                    'categories.translations' => fn ($q) => $q->where('locale', $locale),
                    'tags.translations' => fn ($q) => $q->where('locale', $locale),
                    'featuredImage',
                    'media',
                    'series.translations' => fn ($q) => $q->where('locale', $locale),
                ])
                ->first();
        });
    }

    /**
     * @return Collection<int, Post>
     */
    public function getRelated(Post $post, int $limit = 4): Collection
    {
        return Cache::remember('post.related.'.$post->id, now()->addHour(), function () use ($post, $limit): Collection {
            $locale = app()->getLocale();
            $categoryIds = $post->categories()->pluck('categories.id');

            if ($categoryIds->isEmpty()) {
                return collect();
            }

            return Post::query()
                ->published()
                ->whereKeyNot($post->id)
                ->whereHas('categories', fn (Builder $q) => $q->whereIn('categories.id', $categoryIds))
                ->with([
                    'translations' => fn ($q) => $q->where('locale', $locale),
                    'featuredImage',
                ])
                ->latest('published_at')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * @return Collection<int, Post>
     */
    public function getBySeries(Series $series, string $locale): Collection
    {
        return Post::query()
            ->published()
            ->where('series_id', $series->id)
            ->with([
                'translations' => fn ($q) => $q->where('locale', $locale),
                'featuredImage',
            ])
            ->orderBy('series_order')
            ->orderBy('published_at')
            ->get();
    }

    public function incrementViewCount(Post $post): void
    {
        $cacheKey = 'post.viewed.'.$post->id.'.'.session()->getId();

        if (Cache::has($cacheKey)) {
            return;
        }

        Cache::put($cacheKey, true, now()->addHour());
        $post->increment('view_count');
    }

    public function findCategoryBySlug(string $slug, string $locale): ?Category
    {
        return Category::query()
            ->where('is_active', true)
            ->whereHas('translations', fn (Builder $q) => $q->where('locale', $locale)->where('slug', $slug))
            ->with(['translations' => fn ($q) => $q->where('locale', $locale), 'cover'])
            ->first();
    }

    public function findTagBySlug(string $slug, string $locale): ?Tag
    {
        return Tag::query()
            ->where('is_active', true)
            ->whereHas('translations', fn (Builder $q) => $q->where('locale', $locale)->where('slug', $slug))
            ->with(['translations' => fn ($q) => $q->where('locale', $locale)])
            ->first();
    }

    public function findSeriesBySlug(string $slug, string $locale): ?Series
    {
        return Series::query()
            ->where('is_active', true)
            ->whereHas('translations', fn (Builder $q) => $q->where('locale', $locale)->where('slug', $slug))
            ->with(['translations' => fn ($q) => $q->where('locale', $locale), 'cover'])
            ->first();
    }
}
