<?php

namespace App\Domain\User\Listeners;

use App\Domain\User\Models\User;
use Illuminate\Auth\Events\Login;

class UpdateLastLoginOnLogin
{
    /**
     * Catat waktu dan IP login terakhir.
     */
    public function handle(Login $event): void
    {
        if (! $event->user instanceof User) {
            return;
        }

        $event->user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ])->saveQuietly();
    }
}
