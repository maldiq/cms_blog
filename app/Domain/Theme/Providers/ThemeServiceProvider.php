<?php

namespace App\Domain\Theme\Providers;

use App\Domain\Theme\Models\Theme;
use App\Domain\Theme\Services\ThemeService;
use App\View\Components\Theme\PageHero;
use App\View\Components\Theme\Sections\AboutSection;
use App\View\Components\Theme\Sections\BlogSection;
use App\View\Components\Theme\Sections\ContactSection;
use App\View\Components\Theme\Sections\CtaSection;
use App\View\Components\Theme\Sections\FaqSection;
use App\View\Components\Theme\Sections\Hero;
use App\View\Components\Theme\Sections\NewsletterSection;
use App\View\Components\Theme\Sections\PricingSection;
use App\View\Components\Theme\Sections\PortfolioSection;
use App\View\Components\Theme\Sections\ProcessSection;
use App\View\Components\Theme\Sections\ServicesSection;
use App\View\Components\Theme\Sections\StatsSection;
use App\View\Components\Theme\Sections\TeamSection;
use App\View\Components\Theme\Sections\TestimonialSection;
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
            return "<?php echo e(theme_locale({$expression})); ?>";
        });

        Blade::componentNamespace('App\\View\\Components\\Theme', 'theme');
        Blade::component(PageHero::class, 'theme::page-hero');
        Blade::component(Hero::class, 'theme::hero');
        Blade::component(StatsSection::class, 'theme::stats-section');
        Blade::component(CtaSection::class, 'theme::cta-section');
        Blade::component(ServicesSection::class, 'theme::services-section');
        Blade::component(AboutSection::class, 'theme::about-section');
        Blade::component(ProcessSection::class, 'theme::process-section');
        Blade::component(PortfolioSection::class, 'theme::portfolio-section');
        Blade::component(TeamSection::class, 'theme::team-section');
        Blade::component(TestimonialSection::class, 'theme::testimonial-section');
        Blade::component(PricingSection::class, 'theme::pricing-section');
        Blade::component(FaqSection::class, 'theme::faq-section');
        Blade::component(BlogSection::class, 'theme::blog-section');
        Blade::component(NewsletterSection::class, 'theme::newsletter-section');
        Blade::component(ContactSection::class, 'theme::contact-section');
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
