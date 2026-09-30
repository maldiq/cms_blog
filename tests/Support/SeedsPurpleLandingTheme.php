<?php

namespace Tests\Support;

use App\Domain\Theme\Models\Theme;
use App\Domain\Theme\Providers\ThemeServiceProvider;
use Database\Seeders\DefaultContentSeeder;
use Database\Seeders\PageSeeder;
use Database\Seeders\PurpleLandingThemeSeeder;
use Database\Seeders\ServiceProBlogSeeder;
use Illuminate\Support\Facades\Cache;

trait SeedsPurpleLandingTheme
{
    protected function seedPurpleLandingEnvironment(): Theme
    {
        $this->seed(DefaultContentSeeder::class);
        $this->seed(PageSeeder::class);
        $this->seed(PurpleLandingThemeSeeder::class);
        $this->seed(ServiceProBlogSeeder::class);

        Theme::clearCache();
        Cache::flush();

        $theme = Theme::query()->where('slug', 'purplelanding')->where('is_active', true)->firstOrFail();

        /** @var ThemeServiceProvider $provider */
        $provider = $this->app->getProvider(ThemeServiceProvider::class);
        $provider->registerThemeViewNamespace('purplelanding');

        return $theme;
    }
}
