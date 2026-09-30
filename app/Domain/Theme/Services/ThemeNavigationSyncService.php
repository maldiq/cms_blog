<?php

namespace App\Domain\Theme\Services;

use App\Domain\Menu\Services\HeaderMenuFromContentService;
use Database\Seeders\PurpleLandingThemeSeeder;
use Database\Seeders\ServiceProThemeSeeder;

/**
 * Menu header/footer global — disesuaikan saat theme aktif diganti.
 */
class ThemeNavigationSyncService
{
    public function syncForSlug(string $slug): void
    {
        if ($slug === 'purplelanding') {
            app(PurpleLandingThemeSeeder::class)->syncNavigationMenus();

            return;
        }

        if (in_array($slug, ['servicepro', 'cryptopro'], true)) {
            app(ServiceProThemeSeeder::class)->syncNavigationMenus();

            return;
        }

        app(HeaderMenuFromContentService::class)->sync();
    }
}
