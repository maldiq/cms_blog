<?php

namespace App\Domain\Menu\Services;

use App\Domain\Blog\Post\Models\Post;
use App\Domain\Menu\Models\Menu;
use App\Domain\Menu\Models\MenuItem;
use App\Domain\Page\Models\Page;
use App\Domain\Service\Models\Service;
use App\Domain\Service\Support\ServiceDemoCatalog;
class HeaderMenuFromContentService
{
    public function sync(): void
    {
        ServiceDemoCatalog::seedIfMissing();

        $this->syncHeaderMenu();
        $this->syncHeaderCtaMenu();

        MenuService::clearCacheForLocation('header');
        MenuService::clearCacheForLocation('header-cta');
    }

    protected function syncHeaderMenu(): void
    {
        $menu = Menu::query()->updateOrCreate(
            ['location' => 'header', 'is_active' => true],
            ['name' => 'Menu Header (konten)'],
        );

        MenuItem::query()->where('menu_id', $menu->id)->delete();

        $sortOrder = 1;

        $this->createLinkItem($menu->id, null, $sortOrder++, [
            'id' => 'Beranda',
            'en' => 'Home',
        ], '/');

        $servicesParent = $this->createLinkItem($menu->id, null, $sortOrder++, [
            'id' => 'Layanan',
            'en' => 'Services',
        ], '/services');

        $childOrder = 1;

        $services = ServiceDemoCatalog::forHeaderMenu();

        if ($services->isNotEmpty()) {
            foreach ($services as $service) {
                $this->createServiceItem($menu->id, $servicesParent->id, $childOrder++, $service);
            }
        } else {
            $childOrder = $this->appendPageFallbackChildren($menu->id, $servicesParent->id, $childOrder);
        }

        if ($this->hasPublishedPosts()) {
            $this->createLinkItem($menu->id, null, $sortOrder++, [
                'id' => 'Blog',
                'en' => 'Blog',
            ], '/blog');
        }
    }

    protected function syncHeaderCtaMenu(): void
    {
        $menu = Menu::query()->updateOrCreate(
            ['location' => 'header-cta', 'is_active' => true],
            ['name' => 'Header CTA (konten)'],
        );

        MenuItem::query()->where('menu_id', $menu->id)->delete();

        MenuItem::query()->create([
            'menu_id' => $menu->id,
            'parent_id' => null,
            'type' => MenuItem::TYPE_LINK,
            'target_id' => null,
            'url' => '/contact',
            'label' => ['id' => 'Hubungi Kami', 'en' => 'Contact Us'],
            'icon' => null,
            'target' => '_self',
            'roles' => null,
            'sort_order' => 1,
            'is_active' => true,
        ]);
    }

    protected function appendPageFallbackChildren(int $menuId, int $parentId, int $startOrder): int
    {
        $order = $startOrder;

        $pages = Page::query()
            ->where('status', Page::STATUS_PUBLISHED)
            ->orderBy('id')
            ->with('translations')
            ->get()
            ->filter(function (Page $page): bool {
                $translation = $page->translate('id', false);

                if ($translation === null) {
                    return false;
                }

                return trim(strip_tags((string) $translation->content)) !== '';
            })
            ->take(8);

        foreach ($pages as $page) {
            $idTitle = (string) ($page->translate('id', false)?->title ?? '');
            $enTitle = (string) ($page->translate('en', false)?->title ?? $idTitle);

            MenuItem::query()->create([
                'menu_id' => $menuId,
                'parent_id' => $parentId,
                'type' => MenuItem::TYPE_PAGE,
                'target_id' => $page->id,
                'url' => null,
                'label' => [
                    'id' => $idTitle,
                    'en' => $enTitle,
                ],
                'icon' => null,
                'target' => '_self',
                'roles' => null,
                'sort_order' => $order++,
                'is_active' => true,
            ]);
        }

        return $order;
    }

    protected function hasPublishedPosts(): bool
    {
        return Post::query()
            ->where('status', Post::STATUS_PUBLISHED)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->exists();
    }

    /**
     * @param  array{id: string, en: string}  $label
     */
    protected function createLinkItem(int $menuId, ?int $parentId, int $sortOrder, array $label, string $path): MenuItem
    {
        return MenuItem::query()->create([
            'menu_id' => $menuId,
            'parent_id' => $parentId,
            'type' => MenuItem::TYPE_LINK,
            'target_id' => null,
            'url' => $path,
            'label' => $label,
            'icon' => null,
            'target' => '_self',
            'roles' => null,
            'sort_order' => $sortOrder,
            'is_active' => true,
        ]);
    }

    protected function createServiceItem(int $menuId, int $parentId, int $sortOrder, Service $service): MenuItem
    {
        $idTitle = (string) ($service->translate('id', false)?->title ?? '');
        $enTitle = (string) ($service->translate('en', false)?->title ?? $idTitle);

        return MenuItem::query()->create([
            'menu_id' => $menuId,
            'parent_id' => $parentId,
            'type' => MenuItem::TYPE_SERVICE,
            'target_id' => $service->id,
            'url' => null,
            'label' => [
                'id' => $idTitle,
                'en' => $enTitle,
            ],
            'icon' => null,
            'target' => '_self',
            'roles' => null,
            'sort_order' => $sortOrder,
            'is_active' => true,
        ]);
    }
}
