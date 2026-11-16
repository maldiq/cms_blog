<?php

namespace App\Domain\Affiliate\Policies;

use App\Domain\Affiliate\Models\AffiliateTrafficSource;
use App\Domain\User\Models\User;

class AffiliateTrafficSourcePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor', 'author'])
            || $user->can('affiliate.source.viewAny');
    }

    public function view(User $user, AffiliateTrafficSource $source): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor', 'author'])
            || $user->can('affiliate.source.create');
    }

    public function update(User $user, AffiliateTrafficSource $source): bool
    {
        return $this->create($user);
    }

    public function delete(User $user, AffiliateTrafficSource $source): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin'])
            || $user->can('affiliate.source.delete');
    }
}
