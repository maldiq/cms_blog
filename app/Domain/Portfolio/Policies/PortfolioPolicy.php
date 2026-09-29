<?php

namespace App\Domain\Portfolio\Policies;

use App\Domain\Portfolio\Models\Portfolio;
use App\Domain\User\Models\User;

class PortfolioPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor']) || $user->can('portfolio.portfolio.viewAny');
    }

    public function view(User $user, Portfolio $portfolio): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor']) || $user->can('portfolio.portfolio.create');
    }

    public function update(User $user, Portfolio $portfolio): bool
    {
        return $this->create($user);
    }

    public function delete(User $user, Portfolio $portfolio): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']) || $user->can('portfolio.portfolio.delete');
    }
}
