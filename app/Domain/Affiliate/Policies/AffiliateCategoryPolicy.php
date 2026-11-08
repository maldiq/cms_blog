<?php

namespace App\Domain\Affiliate\Policies;

use App\Domain\Affiliate\Models\AffiliateCategory;
use App\Domain\User\Models\User;

class AffiliateCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor'])
            || $user->can('affiliate.category.viewAny');
    }

    public function view(User $user, AffiliateCategory $category): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor'])
            || $user->can('affiliate.category.create');
    }

    public function update(User $user, AffiliateCategory $category): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor'])
            || $user->can('affiliate.category.update');
    }

    public function delete(User $user, AffiliateCategory $category): bool
    {
        return $category->products()->doesntExist()
            && ($user->hasAnyRole(['super-admin', 'admin'])
                || $user->can('affiliate.category.delete'));
    }
}
