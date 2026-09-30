<?php

namespace Tests\Feature\Theme;

use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
