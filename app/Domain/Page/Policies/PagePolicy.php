<?php

namespace App\Domain\Page\Policies;

use App\Domain\Page\Models\Page;
use App\Domain\User\Models\User;

class PagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor']) || $user->can('page.viewAny');
    }

    public function view(User $user, Page $page): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor']) || $user->can('page.create');
    }

    public function update(User $user, Page $page): bool
    {
        return $this->create($user);
    }

    public function delete(User $user, Page $page): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']) || $user->can('page.delete');
    }
}
