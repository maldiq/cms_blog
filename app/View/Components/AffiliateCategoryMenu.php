<?php

namespace App\View\Components;

use App\Domain\Affiliate\Models\AffiliateProduct;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class AffiliateCategoryMenu extends Component
{
    /**
     * @var Collection<int, string>
     */
    public Collection $categories;

    public function __construct()
    {
        $this->categories = AffiliateProduct::query()
            ->active()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');
    }

    public function render(): View
    {
        return view('components.affiliate-category-menu');
    }
}
