<?php

namespace Tests\Feature;

use App\Domain\Portfolio\Models\Portfolio;
use App\Domain\Team\Models\Team;
use App\Domain\Testimonial\Models\Testimonial;
use App\Domain\Theme\Models\Theme;
use App\Domain\Theme\Providers\ThemeServiceProvider;
use Database\Seeders\LanguageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ServiceProShowcaseSectionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LanguageSeeder::class);

        Theme::clearCache();
        Cache::flush();
    }

    protected function activateServiceProTheme(): Theme
    {
        $theme = Theme::query()->create([
            'name' => 'ServicePro',
            'slug' => 'servicepro',
            'is_active' => true,
        ]);

        Theme::clearCache();

        /** @var ThemeServiceProvider $provider */
        $provider = $this->app->getProvider(ThemeServiceProvider::class);
        $provider->registerThemeViewNamespace('servicepro');

        return $theme;
    }

    public function test_portfolio_section_renders_featured(): void
    {
        $this->activateServiceProTheme();

        $portfolio = Portfolio::factory()->create([
            'is_featured' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $portfolio->translateOrNew('id')->fill([
            'title' => 'Proyek Alpha',
            'slug' => 'proyek-alpha',
            'excerpt' => 'Ringkasan proyek.',
        ])->save();

        $this->get('/id')
            ->assertOk()
            ->assertSee('Proyek Alpha', false)
            ->assertSee(route('portfolio.show', ['locale' => 'id', 'slug' => 'proyek-alpha']), false);
    }

    public function test_team_section_renders_active(): void
    {
        $this->activateServiceProTheme();

        $team = Team::factory()->create([
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $team->translateOrNew('id')->fill([
            'name' => 'Budi Santoso',
            'slug' => 'budi-santoso',
            'position' => 'Lead Developer',
        ])->save();

        $this->get('/id')
            ->assertOk()
            ->assertSee('Budi Santoso', false)
            ->assertSee('Lead Developer', false);
    }

    public function test_testimonial_section_renders_rating(): void
    {
        $this->activateServiceProTheme();

        $testimonial = Testimonial::factory()->create([
            'rating' => 4,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $testimonial->translateOrNew('id')->fill([
            'author_name' => 'Siti Rahma',
            'author_position' => 'CEO',
            'author_company' => 'Acme',
            'content' => 'Pelayanan sangat memuaskan.',
        ])->save();

        $this->get('/id')
            ->assertOk()
            ->assertSee('Siti Rahma', false)
            ->assertSee('Pelayanan sangat memuaskan.', false)
            ->assertSee('theme-testimonial-swiper', false)
            ->assertSee('text-amber-400', false);
    }

    public function test_empty_data_does_not_error(): void
    {
        $this->activateServiceProTheme();

        $this->get('/id')->assertOk();
    }
}
