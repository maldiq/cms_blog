<?php

namespace App\Domain\Setting\Settings;

use Spatie\LaravelSettings\Settings;

class SeoSettings extends Settings
{
    public ?string $default_meta_title;

    public ?string $default_meta_description;

    public ?string $default_og_image;

    public ?string $google_analytics_id;

    public ?string $google_search_console;

    public static function group(): string
    {
        return 'seo';
    }
}
