<?php

namespace Tests\Support;

use App\Domain\Theme\Models\Theme;
use App\Domain\Theme\Providers\ThemeServiceProvider;
use Database\Seeders\DefaultContentSeeder;
use Database\Seeders\PageSeeder;
use Database\Seeders\ServiceProBlogSeeder;
use Database\Seeders\ServiceProThemeSeeder;
use Illuminate\Support\Facades\Cache;

trait SeedsServiceProTheme
{
    protected function seedServiceProEnvironment(): Theme
    {
        $this->seed(DefaultContentSeeder::class);
        $this->seed(PageSeeder::class);
        $this->seed(ServiceProThemeSeeder::class);
        $this->seed(ServiceProBlogSeeder::class);

        Theme::clearCache();
        Cache::flush();

        $theme = Theme::query()->where('slug', 'servicepro')->where('is_active', true)->firstOrFail();

        /** @var ThemeServiceProvider $provider */
        $provider = $this->app->getProvider(ThemeServiceProvider::class);
        $provider->registerThemeViewNamespace('servicepro');

        return $theme;
    }
}
