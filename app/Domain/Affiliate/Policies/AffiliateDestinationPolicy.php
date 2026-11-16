<?php

namespace App\Domain\Affiliate\Policies;

use App\Domain\Affiliate\Models\AffiliateDestination;
use App\Domain\User\Models\User;

class AffiliateDestinationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor'])
            || $user->can('affiliate.destination.viewAny');
    }

    public function view(User $user, AffiliateDestination $destination): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor'])
            || $user->can('affiliate.destination.create');
    }

    public function update(User $user, AffiliateDestination $destination): bool
    {
        return $this->create($user);
    }

    public function delete(User $user, AffiliateDestination $destination): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin'])
            || $user->can('affiliate.destination.delete');
    }
}
