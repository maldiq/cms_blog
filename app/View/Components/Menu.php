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
        public string $location,
        protected MenuService $menuService,
    ) {
        $tree = $this->menuService->buildTree($location);
        $this->items = $this->menuService->filterByRole($tree, auth()->user());
    }

    public function render(): View
    {
        return view('components.menu');
    }
}
