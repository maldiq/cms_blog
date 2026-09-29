<?php

namespace App\Domain\Team\Policies;

use App\Domain\Team\Models\Team;
use App\Domain\User\Models\User;

class TeamPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor']) || $user->can('team.team.viewAny');
    }

    public function view(User $user, Team $team): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor']) || $user->can('team.team.create');
    }

    public function update(User $user, Team $team): bool
    {
        return $this->create($user);
    }

    public function delete(User $user, Team $team): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']) || $user->can('team.team.delete');
    }
}
