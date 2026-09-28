<?php

namespace App\Domain\Setting\Policies;

use App\Domain\User\Models\User;

class SettingsPolicy
{
    /**
     * Hanya super-admin yang boleh mengakses halaman settings.
     */
    public function manage(User $user): bool
    {
        return $user->hasRole('super-admin');
    }
}
