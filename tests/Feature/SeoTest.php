<?php

namespace Tests\Feature;

use App\Domain\Blog\Category\Models\Category;
use App\Domain\Blog\Post\Models\Post;
use App\Domain\Blog\Tag\Models\Tag;
use App\Domain\Page\Models\Page;
use App\Domain\Seo\Http\Controllers\SitemapController;
use App\Domain\User\Models\User;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            LanguageSeeder::class,
            RolePermissionSeeder::class,
        ]);

        $this->artisan('migrate', ['--path' => 'database/settings', '--force' => true]);

        app()->setLocale('id');
    }

    public function test_sitemap_contains_all_published_urls(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $post = Post::query()->create([
            'user_id' => $user->id,
            'status' => Post::STATUS_PUBLISHED,
            'type' => 'article',
            'published_at' => now(),
        ]);
        $post->translateOrNew('id')->fill(['title' => 'Post SEO', 'slug' => 'post-seo', 'content' => '<p>Hi</p>'])->save();

        $page = Page::query()->create([
            'user_id' => $user->id,
            'status' => Page::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);
        $page->translateOrNew('id')->fill(['title' => 'About', 'slug' => 'about', 'content' => '<p>About</p>'])->save();

        $category = Category::query()->create(['is_active' => true, 'sort_order' => 0]);
        $category->translateOrNew('id')->fill(['name' => 'Tech', 'slug' => 'tech'])->save();

        $tag = Tag::query()->create(['is_active' => true]);
        $tag->translateOrNew('id')->fill(['name' => 'Laravel', 'slug' => 'laravel'])->save();

        SitemapController::forgetCache();
        Cache::flush();

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('content-type', 'application/xml');

        $body = $response->getContent();

        $this->assertStringContainsString(url('/id'), $body);
        $this->assertStringContainsString(url('/id/blog'), $body);
        $this->assertStringContainsString(url('/id/blog/post-seo'), $body);
        $this->assertStringContainsString(url('/id/page/about'), $body);
        $this->assertStringContainsString(url('/id/category/tech'), $body);
        $this->assertStringContainsString(url('/id/tag/laravel'), $body);
        $this->assertStringContainsString(url('/id/gallery'), $body);
    }

    public function test_meta_tag_rendered_correctly(): void
    {
        $user = User::factory()->create(['is_active' => true, 'name' => 'Author']);

        $post = Post::query()->create([
            'user_id' => $user->id,
            'status' => Post::STATUS_PUBLISHED,
            'type' => 'article',
            'published_at' => now(),
        ]);

        $post->translateOrNew('id')->fill([
            'title' => 'Judul Meta',
            'slug' => 'judul-meta',
            'excerpt' => 'Ringkasan meta',
            'meta_title' => 'Custom Meta Title',
            'meta_description' => 'Custom meta description',
            'meta_keywords' => 'laravel,cms',
            'content' => '<p>Konten</p>',
        ])->save();

        $this->get('/id/blog/judul-meta')
            ->assertOk()
            ->assertSee('<meta name="description" content="Custom meta description">', false)
            ->assertSee('<meta property="og:title" content="Custom Meta Title">', false)
            ->assertSee('<meta name="twitter:card" content="summary_large_image">', false)
            ->assertSee('BlogPosting', false);
    }

    public function test_hreflang_alternate_rendered(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $post = Post::query()->create([
            'user_id' => $user->id,
            'status' => Post::STATUS_PUBLISHED,
            'type' => 'article',
            'published_at' => now(),
        ]);

        $post->translateOrNew('id')->fill([
            'title' => 'Judul ID',
            'slug' => 'judul-id',
            'content' => '<p>ID</p>',
        ])->save();

        $post->translateOrNew('en')->fill([
            'title' => 'Title EN',
            'slug' => 'title-en',
            'content' => '<p>EN</p>',
        ])->save();

        $this->get('/id/blog/judul-id')
            ->assertOk()
            ->assertSee('rel="alternate" hreflang="id"', false)
            ->assertSee('rel="alternate" hreflang="en"', false)
            ->assertSee(url('/id/blog/judul-id'), false)
            ->assertSee(url('/en/blog/title-en'), false);
    }
}
