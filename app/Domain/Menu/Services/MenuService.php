<?php

namespace App\Domain\Menu\Services;

use App\Domain\Language\Models\Language;
use App\Domain\Menu\Models\Menu;
use App\Domain\Menu\Models\MenuItem;
use App\Domain\User\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class MenuService
{
    public static function cacheKey(string $location, ?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return "menu.{$location}.{$locale}";
    }

    public static function clearCacheForLocation(string $location): void
    {
        if (Schema::hasTable('languages')) {
            foreach (Language::getActive() as $language) {
                Cache::forget(self::cacheKey($location, $language->code));
            }
        }

        Cache::forget(self::cacheKey($location, 'id'));
        Cache::forget(self::cacheKey($location, 'en'));
    }

    /**
     * Bangun pohon menu untuk lokasi (cached 1 jam).
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function buildTree(string $location): Collection
    {
        $locale = app()->getLocale();
        $key = self::cacheKey($location, $locale);

        /** @var Collection<int, array<string, mixed>> $tree */
        $tree = Cache::remember($key, now()->addHour(), function () use ($location): Collection {
            $menu = Menu::query()->active()->byLocation($location)->first();

            if ($menu === null) {
                return collect();
            }

            $items = $menu->items()->active()->ordered()->get();

            return $this->nestItems($items);
        });

        return $tree;
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $tree
     * @return Collection<int, array<string, mixed>>
     */
    public function filterByRole(Collection $tree, ?User $user): Collection
    {
        return $tree
            ->map(fn (array $node): ?array => $this->filterNodeByRole($node, $user))
            ->filter()
            ->values();
    }

    /**
     * @param  Collection<int, MenuItem>  $items
     * @return Collection<int, array<string, mixed>>
     */
    protected function nestItems(Collection $items, ?int $parentId = null): Collection
    {
        return $items
            ->where('parent_id', $parentId)
            ->values()
            ->map(function (MenuItem $item) use ($items): array {
                $locale = app()->getLocale();
                $label = $item->getTranslation('label', $locale, false)
                    ?: $item->getTranslation('label', 'id', false)
                    ?: '';

                return [
                    'id' => $item->id,
                    'label' => $label,
                    'url' => $this->localizeUrl($item->resolveUrl() ?? '#', $locale),
                    'target' => $item->target,
                    'icon' => $item->icon,
                    'roles' => $item->roles,
                    'children' => $this->nestItems($items, $item->id),
                ];
            });
    }

    /**
     * @param  array<string, mixed>  $node
     * @return array<string, mixed>|null
     */
    protected function filterNodeByRole(array $node, ?User $user): ?array
    {
        /** @var list<string>|null $roles */
        $roles = $node['roles'] ?? null;

        if (filled($roles) && ! $this->userMatchesRoles($user, $roles)) {
            return null;
        }

        /** @var Collection<int, array<string, mixed>> $children */
        $children = collect($node['children'] ?? []);
        $node['children'] = $this->filterByRole($children, $user);

        return $node;
    }

    /**
     * @param  list<string>  $roles
     */
    protected function userMatchesRoles(?User $user, array $roles): bool
    {
        if ($user === null) {
            return false;
        }

        return $user->hasAnyRole($roles);
    }

    /**
     * Path menu tanpa locale (/blog) atau legacy (/id/blog) disesuaikan locale aktif.
     */
    protected function localizeUrl(string $url, string $locale): string
    {
        if ($url === '#' || $url === '') {
            return '#';
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        if (preg_match('#^/([a-z]{2})(/.*)?$#i', $url, $matches)) {
            $path = $matches[2] ?? '';

            return '/'.$locale.($path !== '' ? $path : '');
        }

        if ($url === '/') {
            return '/'.$locale;
        }

        if (str_starts_with($url, '/')) {
            return '/'.$locale.$url;
        }

        return $url;
    }
}
