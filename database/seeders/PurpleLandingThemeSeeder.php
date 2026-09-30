<?php

namespace Database\Seeders;

/**
 * Seed theme Purple Landing (aktifkan + settings + sample).
 * Jalankan: php artisan db:seed --class=PurpleLandingThemeSeeder
 */
class PurpleLandingThemeSeeder extends ServiceProThemeSeeder
{
    public function run(): void
    {
        $theme = parent::activateThemeBySlug('purplelanding');

        $this->call(PurpleLandingThemeSettingsSeeder::class, false, ['theme' => $theme]);

        try {
            app(\App\Domain\Service\Services\SolusiwebServiceImportService::class)->import();
        } catch (\Throwable) {
        }

        $this->ensureMinimumActiveServices(6);
        $this->seedOnePageMenus();

        $this->seedOnePageFooterMenus();
        $this->seedPortfolios();
        $this->seedTeamMembers();
        $this->seedTestimonials();

        \App\Domain\Theme\Models\Theme::clearCache();
    }

    protected function seedOnePageMenus(): void
    {
        $this->seedSimpleFooterMenu('header', 'Header One Page', [
            ['id' => 'Solusi', 'en' => 'Solutions', 'url' => '#solutions'],
            ['id' => 'Tentang', 'en' => 'About', 'url' => '#about'],
            ['id' => 'Harga', 'en' => 'Pricing', 'url' => '#pricing'],
            ['id' => 'Ulasan', 'en' => 'Reviews', 'url' => '#reviews'],
            ['id' => 'Blog', 'en' => 'Blog', 'url' => '#blog'],
            ['id' => 'FAQ', 'en' => 'FAQ', 'url' => '#faq'],
        ]);

        $this->seedSimpleFooterMenu('header-cta', 'Header CTA One Page', [
            ['id' => 'Hubungi Kami', 'en' => 'Get Started', 'url' => '#contact'],
        ]);

        \App\Domain\Menu\Services\MenuService::clearCacheForLocation('header');
        \App\Domain\Menu\Services\MenuService::clearCacheForLocation('header-cta');
    }

    protected function seedOnePageFooterMenus(): void
    {
        $this->seedSimpleFooterMenu('footer-company', 'Footer Perusahaan', [
            ['id' => 'Tentang', 'en' => 'About', 'url' => '#about'],
            ['id' => 'Ulasan', 'en' => 'Reviews', 'url' => '#reviews'],
            ['id' => 'Kontak', 'en' => 'Contact', 'url' => '#contact'],
        ]);

        $this->seedSimpleFooterMenu('footer-services', 'Footer Layanan', [
            ['id' => 'Solusi', 'en' => 'Solutions', 'url' => '#solutions'],
            ['id' => 'Harga', 'en' => 'Pricing', 'url' => '#pricing'],
            ['id' => 'FAQ', 'en' => 'FAQ', 'url' => '#faq'],
        ]);

        $this->seedSimpleFooterMenu('footer-legal', 'Footer Legal', [
            ['id' => 'Blog', 'en' => 'Blog', 'url' => '#blog'],
        ]);

        $this->seedSimpleFooterMenu('footer-bottom', 'Footer Bottom', [
            ['id' => 'Kebijakan Privasi', 'en' => 'Privacy Policy', 'url' => '/page/privacy-policy'],
        ]);
    }
}
