<?php

namespace Tests\Feature;

use App\Domain\Blog\Category\Models\Category;
use App\Domain\Blog\Post\Models\Post;
use App\Domain\Blog\Post\Services\PostService;
use App\Domain\Blog\Series\Models\Series;
use App\Domain\User\Models\User;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            LanguageSeeder::class,
            RolePermissionSeeder::class,
        ]);

        app()->setLocale('id');
    }

    public function test_can_create_post_with_translations(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $post = Post::query()->create([
            'user_id' => $user->id,
            'status' => Post::STATUS_DRAFT,
            'type' => 'article',
        ]);

        $post->translateOrNew('id')->fill([
            'title' => 'Judul ID',
            'slug' => 'judul-id',
            'content' => '<p>Halo</p>',
        ])->save();

        $post->translateOrNew('en')->fill([
            'title' => 'Title EN',
            'slug' => 'title-en',
            'content' => '<p>Hello</p>',
        ])->save();

        $this->assertDatabaseHas('post_translations', ['post_id' => $post->id, 'locale' => 'id', 'slug' => 'judul-id']);
        $this->assertDatabaseHas('post_translations', ['post_id' => $post->id, 'locale' => 'en', 'slug' => 'title-en']);
    }

    public function test_slug_auto_generated_per_locale(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $post = Post::query()->create([
            'user_id' => $user->id,
            'status' => Post::STATUS_DRAFT,
            'type' => 'article',
        ]);

        $translation = $post->translateOrNew('id');
        $translation->title = 'Tips Laravel';
        $translation->slug = '';
        $translation->save();

        $this->assertSame('tips-laravel', $translation->fresh()->slug);
    }

    public function test_published_post_visible_in_frontend(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $post = Post::query()->create([
            'user_id' => $user->id,
            'status' => Post::STATUS_PUBLISHED,
            'type' => 'article',
            'published_at' => now()->subDay(),
        ]);

        $post->translateOrNew('id')->fill([
            'title' => 'Publik',
            'slug' => 'publik',
            'content' => '<p>Isi</p>',
        ])->save();

        $this->get('/id/blog/publik')->assertOk();
    }

    public function test_draft_post_not_visible(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $post = Post::query()->create([
            'user_id' => $user->id,
            'status' => Post::STATUS_DRAFT,
            'type' => 'article',
        ]);

        $post->translateOrNew('id')->fill([
            'title' => 'Draft',
            'slug' => 'draft-post',
            'content' => '<p>Isi</p>',
        ])->save();

        $this->get('/id/blog/draft-post')->assertNotFound();
    }

    public function test_reading_time_calculated(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $post = Post::query()->create([
            'user_id' => $user->id,
            'status' => Post::STATUS_DRAFT,
            'type' => 'article',
        ]);

        $words = implode(' ', array_fill(0, 400, 'word'));

        $post->translateOrNew('id')->fill([
            'title' => 'Reading',
            'slug' => 'reading',
            'content' => '<p>'.$words.'</p>',
        ])->save();

        $post->load('translations');
        $post->recalculateReadingTime();
        $post->saveQuietly();

        $this->assertGreaterThanOrEqual(2, (int) $post->fresh()->reading_time);
    }

    public function test_related_posts_returned(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $category = Category::query()->create(['is_active' => true, 'sort_order' => 1]);
        $category->translateOrNew('id')->fill(['name' => 'Cat', 'slug' => 'cat'])->save();

        $main = $this->makePublishedPost($user, 'main', 'main');
        $related = $this->makePublishedPost($user, 'related', 'related');

        $main->categories()->sync([$category->id]);
        $related->categories()->sync([$category->id]);

        $results = app(PostService::class)->getRelated($main, 3);

        $this->assertTrue($results->contains(fn (Post $p) => $p->id === $related->id));
    }

    public function test_series_navigation_works(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $series = Series::query()->create(['is_active' => true]);
        $series->translateOrNew('id')->fill(['title' => 'Series', 'slug' => 'series-nav'])->save();

        $first = $this->makePublishedPost($user, 'Bagian 1', 'bagian-1', $series->id, 1);
        $second = $this->makePublishedPost($user, 'Bagian 2', 'bagian-2', $series->id, 2);

        $this->get('/id/series/series-nav/bagian-2')->assertOk()->assertSee('Bagian 2');

        $posts = app(PostService::class)->getBySeries($series, 'id');
        $this->assertCount(2, $posts);
        $this->assertSame($first->id, $posts->first()->id);
        $this->assertSame($second->id, $posts->last()->id);
    }

    protected function makePublishedPost(User $user, string $title, string $slug, ?int $seriesId = null, ?int $seriesOrder = null): Post
    {
        $post = Post::query()->create([
            'user_id' => $user->id,
            'status' => Post::STATUS_PUBLISHED,
            'type' => 'article',
            'published_at' => now()->subDay(),
            'series_id' => $seriesId,
            'series_order' => $seriesOrder,
        ]);

        $post->translateOrNew('id')->fill([
            'title' => $title,
            'slug' => $slug,
            'content' => '<p>'.Str::random(32).'</p>',
        ])->save();

        return $post;
    }
}
