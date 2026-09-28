<?php

namespace App\Domain\Role\Policies;

use App\Domain\Role\Models\Role;
use App\Domain\User\Models\User;

class RolePolicy
{
    /**
     * Hanya super-admin yang boleh mengelola role.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super-admin');
    }

    public function view(User $user, Role $role): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Role $role): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, Role $role): bool
    {
        return $this->viewAny($user);
    }
}
