<?php

namespace App\Domain\Comment\Policies;

use App\Domain\Comment\Models\Comment;
use App\Domain\User\Models\User;

class CommentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor']) || $user->can('comment.viewAny');
    }

    public function view(User $user, Comment $comment): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Comment $comment): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor']) || $user->can('comment.update');
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']) || $user->can('comment.delete');
    }
}
