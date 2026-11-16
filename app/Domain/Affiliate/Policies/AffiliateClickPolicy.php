<?php

namespace App\Domain\Affiliate\Policies;

use App\Domain\User\Models\User;

class AffiliateClickPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor'])
            || $user->can('affiliate.click.viewAny');
    }

    public function view(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user): bool
    {
        return false;
    }

    public function delete(User $user): bool
    {
        return false;
    }

    public function export(User $user): bool
    {
        return $this->viewAny($user)
            && ($user->hasAnyRole(['super-admin', 'admin']) || $user->can('affiliate.click.export'));
    }
}
