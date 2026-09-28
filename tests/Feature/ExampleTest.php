<?php

namespace Tests\Feature;

use Database\Seeders\LanguageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LanguageSeeder::class);
    }

    public function test_the_application_returns_a_successful_response(): void
    {
        $this->get('/')
            ->assertRedirect('/id');

        $this->get('/id')
            ->assertSuccessful();
    }
}
