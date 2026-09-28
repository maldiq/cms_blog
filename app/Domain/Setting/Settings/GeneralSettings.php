<?php

namespace App\Domain\Setting\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $site_name;

    public ?string $site_description;

    public ?int $site_logo;

    public ?int $site_favicon;

    public string $default_locale;

    public string $timezone;

    public string $date_format;

    public static function group(): string
    {
        return 'general';
    }
}
