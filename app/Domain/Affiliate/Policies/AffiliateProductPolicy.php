<?php

namespace App\Domain\Affiliate\Policies;

use App\Domain\Affiliate\Models\AffiliateProduct;
use App\Domain\User\Models\User;

class AffiliateProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor'])
            || $user->can('affiliate.product.viewAny');
    }

    public function view(User $user, AffiliateProduct $product): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor'])
            || $user->can('affiliate.product.create');
    }

    public function update(User $user, AffiliateProduct $product): bool
    {
        return $this->create($user);
    }

    public function delete(User $user, AffiliateProduct $product): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin'])
            || $user->can('affiliate.product.delete');
    }
}
