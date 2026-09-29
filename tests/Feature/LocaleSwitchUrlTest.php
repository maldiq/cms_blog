<?php

namespace Tests\Feature;

use Database\Seeders\LanguageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleSwitchUrlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LanguageSeeder::class);
    }

    public function test_header_switch_url_replaces_locale_segment(): void
    {
        $response = $this->get('/id');

        $response->assertSuccessful();
        $response->assertSee('href="/en"', false);
    }
}
