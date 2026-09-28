<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_name', 'CMS Blog');
        $this->migrator->add('general.site_description', null);
        $this->migrator->add('general.site_logo', null);
        $this->migrator->add('general.site_favicon', null);
        $this->migrator->add('general.default_locale', 'id');
        $this->migrator->add('general.timezone', 'Asia/Jakarta');
        $this->migrator->add('general.date_format', 'd M Y');

        $this->migrator->add('social.facebook', null);
        $this->migrator->add('social.twitter', null);
        $this->migrator->add('social.instagram', null);
        $this->migrator->add('social.youtube', null);
        $this->migrator->add('social.linkedin', null);
        $this->migrator->add('social.tiktok', null);

        $this->migrator->add('seo.default_meta_title', null);
        $this->migrator->add('seo.default_meta_description', null);
        $this->migrator->add('seo.default_og_image', null);
        $this->migrator->add('seo.google_analytics_id', null);
        $this->migrator->add('seo.google_search_console', null);

        $this->migrator->add('comment.auto_approve', false);
        $this->migrator->add('comment.max_depth', 3);
        $this->migrator->add('comment.require_email', true);
        $this->migrator->add('comment.notify_admin', true);
        $this->migrator->add('comment.admin_email', 'admin@admin.com');

        $this->migrator->add('mail.driver', 'log');
        $this->migrator->add('mail.host', null);
        $this->migrator->add('mail.port', null);
        $this->migrator->add('mail.username', null);
        $this->migrator->add('mail.password', null);
        $this->migrator->add('mail.encryption', null);
        $this->migrator->add('mail.from_address', 'hello@example.com');
        $this->migrator->add('mail.from_name', 'CMS Blog');
    }
};
