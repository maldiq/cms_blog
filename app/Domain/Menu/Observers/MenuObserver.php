<?php

namespace App\Domain\Menu\Observers;

use App\Domain\Menu\Models\Menu;
use App\Domain\Menu\Services\MenuService;

class MenuObserver
{
    public function saved(Menu $menu): void
    {
        MenuService::clearCacheForLocation($menu->location);
    }

    public function deleted(Menu $menu): void
    {
        MenuService::clearCacheForLocation($menu->location);
    }
}
