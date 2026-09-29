<?php

namespace Tests\Feature;

use App\Domain\Newsletter\Livewire\NewsletterForm;
use App\Domain\Newsletter\Models\NewsletterSubscriber;
use App\Domain\User\Models\User;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\SuperAdminSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

class NewsletterSubscriberTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            LanguageSeeder::class,
            RolePermissionSeeder::class,
            SuperAdminSeeder::class,
        ]);

        Filament::setCurrentPanel(Filament::getPanel('admin'));
        app()->setLocale('id');
    }

    public function test_can_subscribe_newsletter(): void
    {
        Livewire::test(NewsletterForm::class)
            ->set('email', 'baru@example.com')
            ->call('subscribe')
            ->assertHasNoErrors()
            ->assertSet('subscribed', true);

        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => 'baru@example.com',
            'locale' => 'id',
            'is_active' => true,
        ]);
    }

    public function test_cannot_subscribe_duplicate_email(): void
    {
        NewsletterSubscriber::factory()->create(['email' => 'duplikat@example.com']);

        Livewire::test(NewsletterForm::class)
            ->set('email', 'duplikat@example.com')
            ->call('subscribe')
            ->assertHasErrors(['email' => 'unique']);

        $this->assertSame(1, NewsletterSubscriber::query()->where('email', 'duplikat@example.com')->count());
    }

    public function test_rate_limit_blocks_flood(): void
    {
        RateLimiter::clear('newsletter-subscribe:127.0.0.1');

        for ($i = 1; $i <= 3; $i++) {
            Livewire::test(NewsletterForm::class)
                ->set('email', "user{$i}@example.com")
                ->call('subscribe')
                ->assertHasNoErrors();
        }

        Livewire::test(NewsletterForm::class)
            ->set('email', 'user4@example.com')
            ->call('subscribe')
            ->assertSet('subscribed', false)
            ->assertSet('errorMessage', __('messages.newsletter_rate_limit'));

        $this->assertSame(3, NewsletterSubscriber::query()->count());
    }

    public function test_admin_can_view_subscribers(): void
    {
        NewsletterSubscriber::factory()->count(2)->create();

        $admin = User::query()->where('email', 'admin@admin.com')->firstOrFail();

        $this->actingAs($admin)
            ->get('/kelola/newsletter-subscribers')
            ->assertOk();
    }
}
