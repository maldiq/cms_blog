<?php

namespace App\Domain\Affiliate\Policies;

use App\Domain\Affiliate\Models\AffiliateBanner;
use App\Domain\User\Models\User;

class AffiliateBannerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor'])
            || $user->can('affiliate.banner.viewAny');
    }

    public function view(User $user, AffiliateBanner $banner): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor'])
            || $user->can('affiliate.banner.create');
    }

    public function update(User $user, AffiliateBanner $banner): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor'])
            || $user->can('affiliate.banner.update');
    }

    public function delete(User $user, AffiliateBanner $banner): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin'])
            || $user->can('affiliate.banner.delete');
    }
}
