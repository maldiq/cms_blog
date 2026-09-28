<?php

namespace Tests\Feature;

use App\Domain\Language\Models\Language;
use Database\Seeders\LanguageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LanguageLocaleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LanguageSeeder::class);
    }

    public function test_default_locale_is_id(): void
    {
        $this->get('/')
            ->assertRedirect('/id');

        $this->get('/id')
            ->assertSuccessful()
            ->assertSee(__('messages.welcome', [], 'id'), false);
    }

    public function test_can_switch_locale_via_url(): void
    {
        $this->get('/en')
            ->assertSuccessful()
            ->assertSee(__('messages.welcome', [], 'en'), false);
    }

    public function test_inactive_language_not_accessible(): void
    {
        Language::query()->where('code', 'en')->update(['is_active' => false]);

        $this->get('/en')->assertNotFound();
    }
}
