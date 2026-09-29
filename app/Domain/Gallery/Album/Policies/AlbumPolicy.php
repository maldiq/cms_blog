<?php

namespace App\Domain\Gallery\Album\Policies;

use App\Domain\Gallery\Album\Models\Album;
use App\Domain\User\Models\User;

class AlbumPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor']) || $user->can('gallery.album.viewAny');
    }

    public function view(User $user, Album $album): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor']) || $user->can('gallery.album.create');
    }

    public function update(User $user, Album $album): bool
    {
        return $this->create($user);
    }

    public function delete(User $user, Album $album): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']) || $user->can('gallery.album.delete');
    }
}
