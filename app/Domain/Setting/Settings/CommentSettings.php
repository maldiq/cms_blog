<?php

namespace App\Domain\Setting\Settings;

use Spatie\LaravelSettings\Settings;

class CommentSettings extends Settings
{
    public bool $auto_approve;

    public int $max_depth;

    public bool $require_email;

    public bool $notify_admin;

    public ?string $admin_email;

    public static function group(): string
    {
        return 'comment';
    }
}
