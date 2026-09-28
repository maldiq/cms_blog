<?php

namespace App\Domain\Setting\Settings;

use Spatie\LaravelSettings\Settings;

class SocialSettings extends Settings
{
    public ?string $facebook;

    public ?string $twitter;

    public ?string $instagram;

    public ?string $youtube;

    public ?string $linkedin;

    public ?string $tiktok;

    public static function group(): string
    {
        return 'social';
    }
}
