<?php

namespace App\Domain\Media\Policies;

use App\Domain\Media\Models\Media;
use App\Domain\User\Models\User;

class MediaPolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->hasAnyRole(['super-admin', 'admin', 'editor'])) {
            return true;
        }

        return $user->can('media.viewAny');
    }

    public function view(User $user, Media $media): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        if ($user->hasAnyRole(['super-admin', 'admin', 'editor'])) {
            return true;
        }

        return $user->can('media.upload');
    }

    public function update(User $user, Media $media): bool
    {
        return false;
    }

    public function delete(User $user, Media $media): bool
    {
        if ($user->hasAnyRole(['super-admin', 'admin'])) {
            return true;
        }

        return $user->can('media.delete');
    }
}
