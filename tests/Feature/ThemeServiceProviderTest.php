<?php

namespace Tests\Feature;

use App\Domain\Theme\Models\Theme;
use App\Domain\Theme\Providers\ThemeServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class ThemeServiceProviderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Theme::clearCache();
        Cache::flush();
    }

    public function test_view_namespace_registered_when_theme_active(): void
    {
        Theme::query()->create([
            'name' => 'ServicePro',
            'slug' => 'servicepro',
            'is_active' => true,
        ]);

        Theme::clearCache();

        $this->assertInstanceOf(Theme::class, Theme::current());

        /** @var ThemeServiceProvider $provider */
        $provider = $this->app->getProvider(ThemeServiceProvider::class);
        $provider->registerThemeViewNamespace('servicepro');

        $this->assertTrue(View::exists('theme::pages.home'));
    }

    public function test_provider_does_not_error_when_no_active_theme(): void
    {
        $this->refreshApplication();

        $shared = View::shared('activeTheme');

        $this->assertNull($shared);
        $this->assertTrue(true);
    }
}
