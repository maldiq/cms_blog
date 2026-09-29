<?php

namespace App\Domain\Blog\Post\Policies;

use App\Domain\Blog\Post\Models\Post;
use App\Domain\User\Models\User;

class PostPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor', 'author']) || $user->can('blog.post.viewAny');
    }

    public function view(User $user, Post $post): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor', 'author']) || $user->can('blog.post.create');
    }

    public function update(User $user, Post $post): bool
    {
        return $this->create($user);
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']) || $user->can('blog.post.delete');
    }
}
