<?php

namespace App\Domain\Language\Policies;

use App\Domain\Language\Models\Language;
use App\Domain\User\Models\User;

class LanguagePolicy
{
    /**
     * Hanya super-admin yang boleh mengelola bahasa.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super-admin');
    }

    public function view(User $user, Language $language): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Language $language): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, Language $language): bool
    {
        return $this->viewAny($user);
    }
}
