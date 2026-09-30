<?php

namespace Database\Seeders;

/**
 * Seed theme CryptoPro (aktifkan + settings + sample opsional).
 * Jalankan: php artisan db:seed --class=CryptoProThemeSeeder
 */
class CryptoProThemeSeeder extends ServiceProThemeSeeder
{
    public function run(): void
    {
        $theme = $this->activateThemeBySlug('cryptopro');

        $this->call(CryptoProThemeSettingsSeeder::class, false, ['theme' => $theme]);

        try {
            app(\App\Domain\Service\Services\SolusiwebServiceImportService::class)->import();
        } catch (\Throwable) {
        }

        $this->ensureMinimumActiveServices(6);
        app(\App\Domain\Menu\Services\HeaderMenuFromContentService::class)->sync();

        $this->seedFooterMenus();
        $this->seedPortfolios();
        $this->seedTeamMembers();
        $this->seedTestimonials();

        \App\Domain\Theme\Models\Theme::clearCache();
    }

    protected function activateThemeBySlug(string $slug): \App\Domain\Theme\Models\Theme
    {
        $manifestPath = resource_path('views/themes/'.$slug.'/theme.json');
        $manifest = is_file($manifestPath)
            ? json_decode((string) file_get_contents($manifestPath), true)
            : [];

        \App\Domain\Theme\Models\Theme::query()->update(['is_active' => false]);

        $theme = \App\Domain\Theme\Models\Theme::query()->updateOrCreate(
            ['slug' => $slug],
            [
                'name' => $manifest['name'] ?? 'CryptoPro',
                'description' => $manifest['description'] ?? null,
                'author' => $manifest['author'] ?? null,
                'version' => $manifest['version'] ?? '1.0.0',
                'preview_image' => $manifest['preview'] ?? null,
                'is_active' => true,
            ],
        );

        \App\Domain\Theme\Models\Theme::clearCache();

        return $theme;
    }
}
