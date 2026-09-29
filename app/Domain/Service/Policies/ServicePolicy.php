<?php

namespace App\Domain\Service\Policies;

use App\Domain\Service\Models\Service;
use App\Domain\User\Models\User;

class ServicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor']) || $user->can('service.service.viewAny');
    }

    public function view(User $user, Service $service): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin', 'editor']) || $user->can('service.service.create');
    }

    public function update(User $user, Service $service): bool
    {
        return $this->create($user);
    }

    public function delete(User $user, Service $service): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin']) || $user->can('service.service.delete');
    }
}
