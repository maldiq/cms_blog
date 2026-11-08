<?php

namespace App\Domain\Affiliate\Policies;

use App\Domain\Affiliate\Models\AffiliatePlatform;
use App\Domain\User\Models\User;

class AffiliatePlatformPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor'])
            || $user->can('affiliate.platform.viewAny');
    }

    public function view(User $user, AffiliatePlatform $platform): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor'])
            || $user->can('affiliate.platform.create');
    }

    public function update(User $user, AffiliatePlatform $platform): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor'])
            || $user->can('affiliate.platform.update');
    }

    public function delete(User $user, AffiliatePlatform $platform): bool
    {
        return $platform->products()->doesntExist()
            && ($user->hasAnyRole(['super-admin', 'admin'])
                || $user->can('affiliate.platform.delete'));
    }
}
