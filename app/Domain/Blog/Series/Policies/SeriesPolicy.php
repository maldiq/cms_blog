<?php

namespace App\Domain\Blog\Series\Policies;

use App\Domain\Blog\Series\Models\Series;
use App\Domain\User\Models\User;

class SeriesPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor']) || $user->can('blog.series.viewAny');
    }

    public function view(User $user, Series $series): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor']) || $user->can('blog.series.create');
    }

    public function update(User $user, Series $series): bool
    {
        return $this->create($user);
    }

    public function delete(User $user, Series $series): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']) || $user->can('blog.series.delete');
    }
}
