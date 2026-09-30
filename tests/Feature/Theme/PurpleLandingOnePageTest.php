<?php

namespace Tests\Feature\Theme;

use App\Domain\Theme\Filament\Pages\ThemeSelector;
use App\Domain\User\Models\User;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Support\SeedsPurpleLandingTheme;
use Tests\TestCase;

class PurpleLandingOnePageTest extends TestCase
{
    use RefreshDatabase;
    use SeedsPurpleLandingTheme;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedPurpleLandingEnvironment();
    }

    public function test_homepage_has_one_page_section_anchors(): void
    {
        $response = $this->get('/id');

        $response->assertOk();
        $response->assertSee('id="top"', false);
        $response->assertSee('id="solutions"', false);
        $response->assertSee('id="about"', false);
        $response->assertSee('id="pricing"', false);
        $response->assertSee('id="reviews"', false);
        $response->assertSee('id="blog"', false);
        $response->assertSee('id="faq"', false);
        $response->assertSee('id="contact"', false);
        $response->assertSee('href="#contact"', false);
    }

    public function test_internal_routes_redirect_to_home_fragments(): void
    {
        $this->get('/id/about')->assertRedirect('/id#about');
        $this->get('/id/pricing')->assertRedirect('/id#pricing');
        $this->get('/id/faq')->assertRedirect('/id#faq');
        $this->get('/id/contact')->assertRedirect('/id#contact');
        $this->get('/id/services')->assertRedirect('/id#solutions');
    }

    public function test_blog_post_detail_still_accessible(): void
    {
        $this->get('/id/blog/membangun-cms-modular-laravel')->assertOk();
    }

    public function test_can_switch_theme_from_admin_while_purple_landing_active(): void
    {
        $this->seed([RolePermissionSeeder::class]);

        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');

        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $this->actingAs($admin);

        Livewire::test(ThemeSelector::class)
            ->call('activateTheme', 'servicepro')
            ->assertRedirect();

        $this->assertDatabaseHas('themes', [
            'slug' => 'servicepro',
            'is_active' => 1,
        ]);

        $this->get('/id')
            ->assertOk()
            ->assertSee('/id/services', false)
            ->assertDontSee('href="#solutions"', false);
    }
}
