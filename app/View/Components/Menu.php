<?php

namespace App\View\Components;

use App\Domain\Menu\Services\MenuService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Menu extends Component
{
    /**
     * @var Collection<int, array<string, mixed>>
     */
    public Collection $items;

    public function __construct(
        MenuService $menuService,
        public string $location,
        public bool $cta = false,
    ) {
        $tree = $menuService->buildTree($location);
        $filtered = $menuService->filterByRole($tree, auth()->user());
        $this->items = $this->cta ? $filtered->take(1) : $filtered;
    }

    public function render(): View
    {
        return view($this->cta ? 'components.menu-cta' : 'components.menu');
    }
}
