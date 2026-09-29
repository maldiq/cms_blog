<?php

namespace App\Domain\Menu\Observers;

use App\Domain\Menu\Models\MenuItem;
use App\Domain\Menu\Services\MenuService;

class MenuItemObserver
{
    public function saved(MenuItem $menuItem): void
    {
        $this->clearForItem($menuItem);
    }

    public function deleted(MenuItem $menuItem): void
    {
        $this->clearForItem($menuItem);
    }

    protected function clearForItem(MenuItem $menuItem): void
    {
        $menuItem->loadMissing('menu');

        if ($menuItem->menu !== null) {
            MenuService::clearCacheForLocation($menuItem->menu->location);
        }
    }
}
