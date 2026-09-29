<?php

namespace Tests\Feature;

use App\Domain\Blog\Post\Models\Post;
use App\Domain\Newsletter\Livewire\NewsletterForm;
use App\Domain\Theme\Models\Theme;
use App\Domain\Theme\Models\ThemeSetting;
use App\Domain\Theme\Providers\ThemeServiceProvider;
use App\Domain\User\Models\User;
use Database\Seeders\LanguageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Tests\TestCase;

class ServiceProFinalSectionsTest extends TestCase
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

    public function test_pricing_section_renders_plans(): void
    {
        $theme = $this->activateServiceProTheme();

        ThemeSetting::set($theme->id, 'pricing', 'plans', [
            [
                'name' => ['id' => 'Starter', 'en' => 'Starter'],
                'price' => 'Rp 500.000',
                'period' => 'bulan',
                'is_popular' => true,
                'cta_label' => ['id' => 'Pilih Paket', 'en' => 'Choose Plan'],
                'cta_url' => 'https://example.com/starter',
                'features' => [
                    ['text' => ['id' => 'Fitur A', 'en' => 'Feature A']],
                ],
            ],
        ]);

        $this->get('/id')
            ->assertOk()
            ->assertSee('Starter', false)
            ->assertSee('Rp 500.000', false)
            ->assertSee('Fitur A', false)
            ->assertSee(__('messages.popular'), false);
    }

    public function test_faq_section_renders_items(): void
    {
        $theme = $this->activateServiceProTheme();

        ThemeSetting::set($theme->id, 'faq', 'items', [
            [
                'question' => ['id' => 'Apa layanan utama?', 'en' => 'Main service?'],
                'answer' => ['id' => 'Konsultasi dan implementasi.', 'en' => 'Consulting and implementation.'],
            ],
        ]);

        $this->get('/id')
            ->assertOk()
            ->assertSee('Apa layanan utama?', false)
            ->assertSee('Konsultasi dan implementasi.', false)
            ->assertSee('x-data', false);
    }

    public function test_blog_section_renders_latest_posts(): void
    {
        $theme = $this->activateServiceProTheme();

        ThemeSetting::set($theme->id, 'blog', 'title', [
            'id' => 'Blog Kami',
            'en' => 'Our Blog',
        ]);

        $user = User::factory()->create(['is_active' => true]);

        $post = Post::query()->create([
            'user_id' => $user->id,
            'status' => Post::STATUS_PUBLISHED,
            'type' => 'article',
            'published_at' => now()->subDay(),
        ]);

        $post->translateOrNew('id')->fill([
            'title' => 'Posting Terbaru Satu',
            'slug' => 'posting-terbaru-satu',
            'excerpt' => 'Cuplikan artikel.',
        ])->save();

        $this->get('/id')
            ->assertOk()
            ->assertSee('Blog Kami', false)
            ->assertSee('Posting Terbaru Satu', false)
            ->assertSee(__('messages.all_articles'), false);
    }

    public function test_newsletter_section_renders_form(): void
    {
        $theme = $this->activateServiceProTheme();

        ThemeSetting::set($theme->id, 'newsletter', 'title', [
            'id' => 'Newsletter Mingguan',
            'en' => 'Weekly Newsletter',
        ]);
        ThemeSetting::set($theme->id, 'newsletter', 'placeholder', [
            'id' => 'Email Anda',
            'en' => 'Your email',
        ]);
        ThemeSetting::set($theme->id, 'newsletter', 'button_label', [
            'id' => 'Daftar',
            'en' => 'Sign up',
        ]);

        $this->get('/id')
            ->assertOk()
            ->assertSee('Newsletter Mingguan', false);

        Livewire::test(NewsletterForm::class, [
            'placeholder' => 'Email Anda',
            'buttonLabel' => 'Daftar',
        ])
            ->set('email', 'subscriber@example.com')
            ->call('subscribe')
            ->assertHasNoErrors();
    }
}
