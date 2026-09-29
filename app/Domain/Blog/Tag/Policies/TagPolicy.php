<?php

namespace App\Domain\Blog\Tag\Policies;

use App\Domain\Blog\Tag\Models\Tag;
use App\Domain\User\Models\User;

class TagPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor']) || $user->can('blog.tag.viewAny');
    }

    public function view(User $user, Tag $tag): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor']) || $user->can('blog.tag.create');
    }

    public function update(User $user, Tag $tag): bool
    {
        return $this->create($user);
    }

    public function delete(User $user, Tag $tag): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']) || $user->can('blog.tag.delete');
    }
}
