<?php

namespace Database\Seeders;

use App\Domain\Setting\Settings\CommentSettings;
use App\Domain\Setting\Settings\GeneralSettings;
use App\Domain\Setting\Settings\MailSettings;
use App\Domain\Setting\Settings\SeoSettings;
use App\Domain\Setting\Settings\SocialSettings;
use Illuminate\Database\Seeder;

class AppSettingsSeeder extends Seeder
{
    /**
     * Isi pengaturan aplikasi default (general, social, seo, comment, mail).
     */
    public function run(): void
    {
        app(GeneralSettings::class)->fill([
            'site_name' => 'ServicePro Agency',
            'site_description' => 'Agency layanan web, server, dan dukungan IT profesional untuk bisnis di Indonesia.',
            'site_logo' => null,
            'site_favicon' => null,
            'default_locale' => 'id',
            'timezone' => 'Asia/Jakarta',
            'date_format' => 'd M Y',
        ])->save();

        app(SocialSettings::class)->fill([
            'facebook' => 'https://facebook.com/servicepro.demo',
            'twitter' => null,
            'instagram' => 'https://instagram.com/servicepro.demo',
            'youtube' => 'https://youtube.com/@serviceprodemo',
            'linkedin' => 'https://linkedin.com/company/servicepro-demo',
            'tiktok' => null,
        ])->save();

        app(SeoSettings::class)->fill([
            'default_meta_title' => 'ServicePro Agency — Web & IT Services',
            'default_meta_description' => 'Pengembangan website, infrastruktur cloud, dan maintenance IT dengan tim berpengalaman.',
            'default_og_image' => null,
            'google_analytics_id' => null,
            'google_search_console' => null,
        ])->save();

        app(CommentSettings::class)->fill([
            'auto_approve' => false,
            'max_depth' => 3,
            'require_email' => true,
            'notify_admin' => true,
            'admin_email' => 'admin@admin.com',
            'blocked_words' => 'spam,judi,slot',
        ])->save();

        app(MailSettings::class)->fill([
            'driver' => 'log',
            'host' => null,
            'port' => null,
            'username' => null,
            'password' => null,
            'encryption' => null,
            'from_address' => 'hello@servicepro.demo',
            'from_name' => 'ServicePro Agency',
        ])->save();
    }
}
