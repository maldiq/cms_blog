<?php

namespace Tests\Feature;

use App\Domain\Contact\Livewire\ContactForm;
use App\Domain\Contact\Mail\NewContactSubmissionMail;
use App\Domain\Contact\Models\ContactSubmission;
use App\Domain\Theme\Models\Theme;
use App\Domain\Theme\Models\ThemeSetting;
use App\Domain\User\Models\User;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\SuperAdminSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

class ContactSubmissionTest extends TestCase
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

        $this->seedContactThemeEmail('admin-notify@example.com');
    }

    protected function seedContactThemeEmail(string $email): Theme
    {
        Theme::clearCache();

        $theme = Theme::query()->create([
            'name' => 'ServicePro',
            'slug' => 'servicepro',
            'is_active' => true,
        ]);

        ThemeSetting::set($theme->id, 'contact', 'email', $email);

        Theme::clearCache();

        return $theme;
    }

    /**
     * @return array<string, string>
     */
    protected function validPayload(): array
    {
        return [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'phone' => '08123456789',
            'subject' => 'Penawaran jasa',
            'message' => 'Saya ingin konsultasi website.',
        ];
    }

    public function test_can_submit_contact_form(): void
    {
        Mail::fake();

        Livewire::test(ContactForm::class)
            ->set($this->validPayload())
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('submitted', true);

        $this->assertDatabaseHas('contact_submissions', [
            'email' => 'budi@example.com',
            'subject' => 'Penawaran jasa',
            'status' => ContactSubmission::STATUS_NEW,
            'locale' => 'id',
        ]);
    }

    public function test_honeypot_blocks_bot(): void
    {
        Mail::fake();

        Livewire::test(ContactForm::class)
            ->set($this->validPayload())
            ->set('website', 'http://spam.test')
            ->call('submit')
            ->assertSet('submitted', true);

        $this->assertDatabaseCount('contact_submissions', 0);
        Mail::assertNothingQueued();
    }

    public function test_rate_limit_blocks_flood(): void
    {
        Mail::fake();
        RateLimiter::clear('contact-submit:127.0.0.1');

        for ($i = 1; $i <= 3; $i++) {
            Livewire::test(ContactForm::class)
                ->set([
                    ...$this->validPayload(),
                    'email' => "user{$i}@example.com",
                    'subject' => "Subjek {$i}",
                ])
                ->call('submit')
                ->assertHasNoErrors();
        }

        Livewire::test(ContactForm::class)
            ->set([
                ...$this->validPayload(),
                'email' => 'user4@example.com',
            ])
            ->call('submit')
            ->assertSet('submitted', false)
            ->assertSet('errorMessage', __('messages.contact_rate_limit'));

        $this->assertSame(3, ContactSubmission::query()->count());
        Mail::assertQueued(NewContactSubmissionMail::class, 3);
    }

    public function test_email_sent_to_admin(): void
    {
        Mail::fake();

        Livewire::test(ContactForm::class)
            ->set($this->validPayload())
            ->call('submit');

        Mail::assertQueued(NewContactSubmissionMail::class, function (NewContactSubmissionMail $mail): bool {
            return $mail->hasTo('admin-notify@example.com');
        });
    }

    public function test_admin_can_view_submissions(): void
    {
        ContactSubmission::factory()->count(2)->create();

        $admin = User::query()->where('email', 'admin@admin.com')->firstOrFail();

        $this->actingAs($admin)
            ->get('/kelola/contact-submissions')
            ->assertOk();
    }
}
