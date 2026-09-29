<?php

namespace Tests\Feature;

use App\Domain\Contact\Livewire\ContactForm;
use App\Domain\Portfolio\Models\Portfolio;
use App\Domain\Service\Models\Service;
use App\Domain\Team\Models\Team;
use App\Domain\Testimonial\Models\Testimonial;
use App\Domain\Theme\Models\Theme;
use App\Domain\Theme\Models\ThemeSetting;
use App\Domain\Theme\Providers\ThemeServiceProvider;
use Database\Seeders\LanguageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Tests\TestCase;

class ThemePagesTest extends TestCase
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

    public function test_about_page_renders(): void
    {
        $theme = $this->activateServiceProTheme();

        ThemeSetting::set($theme->id, 'about_page', 'vision', [
            'id' => 'Visi ID',
            'en' => 'Vision EN',
        ]);

        $this->get('/id/about')->assertOk()->assertSee('Visi ID', false);
        $this->get('/en/about')->assertOk()->assertSee('Vision EN', false);
    }

    public function test_services_index_renders_paginated(): void
    {
        $this->activateServiceProTheme();

        $service = Service::factory()->create(['is_active' => true, 'sort_order' => 1]);
        $service->translateOrNew('id')->fill([
            'title' => 'Layanan Index',
            'slug' => 'layanan-index',
        ])->save();

        $this->get('/id/services')
            ->assertOk()
            ->assertSee('Layanan Index', false);
    }

    public function test_service_detail_renders_by_slug(): void
    {
        $this->activateServiceProTheme();

        $service = Service::factory()->create(['is_active' => true, 'price_from' => 1500000]);
        $service->translateOrNew('id')->fill([
            'title' => 'Detail Layanan',
            'slug' => 'detail-layanan',
            'content' => '<p>Konten layanan.</p>',
        ])->save();

        $this->get('/id/services/detail-layanan')
            ->assertOk()
            ->assertSee('Detail Layanan', false)
            ->assertSee('Konten layanan.', false);
    }

    public function test_portfolio_index_renders(): void
    {
        $this->activateServiceProTheme();

        $portfolio = Portfolio::factory()->create(['is_active' => true, 'is_featured' => true]);
        $portfolio->translateOrNew('id')->fill([
            'title' => 'Portfolio Index',
            'slug' => 'portfolio-index',
        ])->save();

        $this->get('/id/portfolio')->assertOk()->assertSee('Portfolio Index', false);
    }

    public function test_portfolio_detail_renders(): void
    {
        $this->activateServiceProTheme();

        $portfolio = Portfolio::factory()->create([
            'is_active' => true,
            'client_name' => 'ACME Corp',
        ]);
        $portfolio->translateOrNew('id')->fill([
            'title' => 'Detail Portfolio',
            'slug' => 'detail-portfolio',
            'content' => '<p>Detail proyek.</p>',
        ])->save();

        $this->get('/id/portfolio/detail-portfolio')
            ->assertOk()
            ->assertSee('Detail Portfolio', false)
            ->assertSee('ACME Corp', false);
    }

    public function test_team_page_renders(): void
    {
        $this->activateServiceProTheme();

        $team = Team::factory()->create(['is_active' => true]);
        $team->translateOrNew('id')->fill([
            'name' => 'Anggota Tim',
            'slug' => 'anggota-tim',
            'position' => 'Designer',
        ])->save();

        $this->get('/id/team')->assertOk()->assertSee('Anggota Tim', false);
    }

    public function test_testimonial_page_renders(): void
    {
        $this->activateServiceProTheme();

        $testimonial = Testimonial::factory()->create(['is_active' => true, 'rating' => 5]);
        $testimonial->translateOrNew('id')->fill([
            'author_name' => 'Reviewer Satu',
            'content' => 'Testimoni bagus.',
        ])->save();

        $this->get('/id/testimonials')->assertOk()->assertSee('Reviewer Satu', false);
    }

    public function test_pricing_page_renders(): void
    {
        $theme = $this->activateServiceProTheme();

        ThemeSetting::set($theme->id, 'pricing', 'title', [
            'id' => 'Paket Harga',
            'en' => 'Pricing Plans',
        ]);

        $this->get('/id/pricing')->assertOk()->assertSee('Paket Harga', false);
    }

    public function test_faq_page_renders(): void
    {
        $theme = $this->activateServiceProTheme();

        ThemeSetting::set($theme->id, 'faq', 'items', [
            [
                'question' => ['id' => 'Pertanyaan umum?', 'en' => 'Common question?'],
                'answer' => ['id' => 'Jawaban singkat.', 'en' => 'Short answer.'],
            ],
        ]);

        $this->get('/id/faq')->assertOk()->assertSee('Pertanyaan umum?', false);
    }

    public function test_contact_page_renders_form(): void
    {
        $theme = $this->activateServiceProTheme();

        ThemeSetting::set($theme->id, 'contact', 'email', 'hello@example.com');

        $this->get('/id/contact')
            ->assertOk()
            ->assertSee('hello@example.com', false)
            ->assertSeeLivewire(ContactForm::class);
    }
}
