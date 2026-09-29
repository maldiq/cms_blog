<?php

namespace App\Domain\Menu\Policies;

use App\Domain\Menu\Models\Menu;
use App\Domain\User\Models\User;

class MenuPolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->hasAnyRole(['super-admin', 'admin'])) {
            return true;
        }

        return $user->can('menu.viewAny');
    }

    public function view(User $user, Menu $menu): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        if ($user->hasAnyRole(['super-admin', 'admin'])) {
            return true;
        }

        return $user->can('menu.create');
    }

    public function update(User $user, Menu $menu): bool
    {
        if ($user->hasAnyRole(['super-admin', 'admin'])) {
            return true;
        }

        return $user->can('menu.update');
    }

    public function delete(User $user, Menu $menu): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return $user->can('menu.delete');
    }
}
