<?php

namespace App\View\Components;

use App\Domain\Affiliate\Models\AffiliateCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class AffiliateCategoryMenu extends Component
{
    /**
     * @var Collection<int, AffiliateCategory>
     */
    public Collection $categories;

    public function __construct()
    {
        $this->categories = AffiliateCategory::query()
            ->where('is_active', true)
            ->whereHas('products', fn ($query) => $query->active())
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['name', 'slug']);
    }

    public function render(): View
    {
        return view('components.affiliate-category-menu');
    }
}
