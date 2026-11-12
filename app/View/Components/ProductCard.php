<?php

namespace App\View\Components;

use App\Domain\Affiliate\Models\AffiliateProduct;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProductCard extends Component
{
    public function __construct(
        public AffiliateProduct $product,
        public string $locale,
    ) {}

    public function render(): View
    {
        return view('components.product-card');
    }
}
