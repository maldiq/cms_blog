<?php

namespace App\Domain\Affiliate\Services;

use App\Domain\Affiliate\Models\AffiliateDestination;
use App\Domain\Affiliate\Models\AffiliateProduct;

class AffiliateDestinationResolver
{
    public function resolve(AffiliateProduct $product): ?AffiliateDestination
    {
        return $product->destinations()
            ->available()
            ->first();
    }

    public function hasDestinations(AffiliateProduct $product): bool
    {
        return $product->destinations()->exists();
    }
}
