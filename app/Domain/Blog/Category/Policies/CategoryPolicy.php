<?php

namespace App\Domain\Blog\Category\Policies;

use App\Domain\Blog\Category\Models\Category;
use App\Domain\User\Models\User;

class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor']) || $user->can('blog.category.viewAny');
    }

    public function view(User $user, Category $category): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor']) || $user->can('blog.category.create');
    }

    public function update(User $user, Category $category): bool
    {
        return $this->create($user);
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']) || $user->can('blog.category.delete');
    }
}
