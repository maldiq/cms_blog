<?php

namespace App\Support\Theme;

use App\Domain\Theme\Models\Theme;
use Database\Seeders\CryptoProThemeSettingsSeeder;
use Database\Seeders\ServiceProThemeSettingsSeeder;
use Illuminate\Database\Seeder;

class ThemeDefaultSettingsSeeder
{
    public static function runForTheme(Theme $theme): void
    {
        $seeder = self::resolve($theme->slug);

        if ($seeder !== null) {
            $seeder->run($theme);
        }
    }

    public static function resolve(string $slug): ?Seeder
    {
        return match ($slug) {
            'servicepro' => app(ServiceProThemeSettingsSeeder::class),
            'cryptopro' => app(CryptoProThemeSettingsSeeder::class),
            default => null,
        };
    }
}
