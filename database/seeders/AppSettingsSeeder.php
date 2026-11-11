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
            'site_name' => 'NainPicks',
            'site_description' => 'Kurasi & rekomendasi produk pilihan dari marketplace global dan terpercaya — global finds, picked for you.',
            'site_logo' => null,
            'site_favicon' => null,
            'default_locale' => 'id',
            'timezone' => 'Asia/Jakarta',
            'date_format' => 'd M Y',
        ])->save();

        app(SocialSettings::class)->fill([
            'facebook' => 'https://facebook.com/nainpicks',
            'twitter' => null,
            'instagram' => 'https://instagram.com/nainpicks',
            'youtube' => 'https://youtube.com/@nainpicks',
            'linkedin' => 'https://linkedin.com/company/nainpicks',
            'tiktok' => null,
        ])->save();

        app(SeoSettings::class)->fill([
            'default_meta_title' => 'NainPicks — Global Finds, Picked for You',
            'default_meta_description' => 'Kurasi produk dan penawaran terbaik dari Shopee, TikTok Shop, Amazon, dan marketplace global lainnya — global finds, picked for you.',
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
            'from_address' => 'hello@naintin.my.id',
            'from_name' => 'NainPicks',
        ])->save();
    }
}
