<?php

use App\Domain\Setting\Settings\CommentSettings;
use App\Domain\Setting\Settings\GeneralSettings;
use App\Domain\Setting\Settings\MailSettings;
use App\Domain\Setting\Settings\SeoSettings;
use App\Domain\Setting\Settings\SocialSettings;

if (! function_exists('setting')) {
    /**
     * Ambil nilai setting berdasarkan key flat (contoh: site_name).
     */
    function setting(string $key, mixed $default = null): mixed
    {
        /** @var array<string, array{class-string, string}> $map */
        $map = [
            'site_name' => [GeneralSettings::class, 'site_name'],
            'site_description' => [GeneralSettings::class, 'site_description'],
            'site_logo' => [GeneralSettings::class, 'site_logo'],
            'site_favicon' => [GeneralSettings::class, 'site_favicon'],
            'default_locale' => [GeneralSettings::class, 'default_locale'],
            'timezone' => [GeneralSettings::class, 'timezone'],
            'date_format' => [GeneralSettings::class, 'date_format'],
            'facebook' => [SocialSettings::class, 'facebook'],
            'twitter' => [SocialSettings::class, 'twitter'],
            'instagram' => [SocialSettings::class, 'instagram'],
            'youtube' => [SocialSettings::class, 'youtube'],
            'linkedin' => [SocialSettings::class, 'linkedin'],
            'tiktok' => [SocialSettings::class, 'tiktok'],
            'default_meta_title' => [SeoSettings::class, 'default_meta_title'],
            'default_meta_description' => [SeoSettings::class, 'default_meta_description'],
            'default_og_image' => [SeoSettings::class, 'default_og_image'],
            'google_analytics_id' => [SeoSettings::class, 'google_analytics_id'],
            'google_search_console' => [SeoSettings::class, 'google_search_console'],
            'auto_approve' => [CommentSettings::class, 'auto_approve'],
            'max_depth' => [CommentSettings::class, 'max_depth'],
            'require_email' => [CommentSettings::class, 'require_email'],
            'notify_admin' => [CommentSettings::class, 'notify_admin'],
            'admin_email' => [CommentSettings::class, 'admin_email'],
            'blocked_words' => [CommentSettings::class, 'blocked_words'],
            'driver' => [MailSettings::class, 'driver'],
            'host' => [MailSettings::class, 'host'],
            'port' => [MailSettings::class, 'port'],
            'username' => [MailSettings::class, 'username'],
            'password' => [MailSettings::class, 'password'],
            'encryption' => [MailSettings::class, 'encryption'],
            'from_address' => [MailSettings::class, 'from_address'],
            'from_name' => [MailSettings::class, 'from_name'],
        ];

        if (! isset($map[$key])) {
            return $default;
        }

        [$settingsClass, $property] = $map[$key];

        try {
            $settings = app($settingsClass);
        } catch (Throwable) {
            return $default;
        }

        $value = $settings->{$property} ?? $default;

        return $value ?? $default;
    }
}
