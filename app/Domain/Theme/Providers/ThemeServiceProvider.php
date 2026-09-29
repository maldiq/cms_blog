<?php

namespace App\Domain\Theme\Providers;

use App\Domain\Theme\Models\Theme;
use App\Domain\Theme\Services\ThemeService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ThemeService::class, fn (): ThemeService => new ThemeService);
    }

    public function boot(): void
    {
        $activeTheme = Theme::current();

        View::share('activeTheme', $activeTheme);

        if ($activeTheme !== null) {
            $this->registerThemeViewNamespace($activeTheme->slug);
        }

        Blade::directive('theme', function (string $expression): string {
            return "<?php echo theme({$expression}); ?>";
        });
    }

    public function registerThemeViewNamespace(string $slug): void
    {
        $path = resource_path('views/themes/' . $slug);

        if (! is_dir($path)) {
            return;
        }

        View::addNamespace('theme', $path);
    }
}
