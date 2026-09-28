<?php

namespace App\Domain\User\Policies;

use App\Domain\User\Models\User;

class UserPolicy
{
    /**
     * Hanya super-admin dan admin yang boleh mengelola user.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']);
    }

    public function view(User $user, User $model): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, User $model): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, User $model): bool
    {
        return $this->viewAny($user);
    }
}
