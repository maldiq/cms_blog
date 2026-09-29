<?php

namespace Tests\Feature;

use App\Domain\Blog\Post\Models\Post;
use App\Domain\Comment\Livewire\CommentForm;
use App\Domain\Comment\Livewire\CommentSection;
use App\Domain\Comment\Mail\NewCommentAdminMail;
use App\Domain\Comment\Models\Comment;
use App\Domain\Comment\Services\CommentService;
use App\Domain\Setting\Settings\CommentSettings;
use App\Domain\User\Models\User;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

class CommentTest extends TestCase
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

    private function createPublishedPost(string $slug = 'post-komentar'): Post
    {
        $user = User::factory()->create(['is_active' => true]);

        $post = Post::query()->create([
            'user_id' => $user->id,
            'status' => Post::STATUS_PUBLISHED,
            'type' => 'article',
            'published_at' => now(),
            'allow_comment' => true,
        ]);

        $post->translateOrNew('id')->fill([
            'title' => 'Post Komentar',
            'slug' => $slug,
            'content' => '<p>Konten</p>',
        ])->save();

        return $post->fresh();
    }

    public function test_guest_can_submit_comment(): void
    {
        Mail::fake();

        $post = $this->createPublishedPost();

        Livewire::test(CommentForm::class, [
            'commentableType' => Post::class,
            'commentableId' => $post->id,
        ])
            ->set('authorName', 'Budi')
            ->set('authorEmail', 'budi@example.com')
            ->set('content', 'Komentar pertama')
            ->call('submit')
            ->assertSet('submitted', true);

        $this->assertDatabaseHas('comments', [
            'commentable_id' => $post->id,
            'author_name' => 'Budi',
            'status' => Comment::STATUS_PENDING,
        ]);

        Mail::assertSent(NewCommentAdminMail::class);
    }

    public function test_comment_requires_moderation(): void
    {
        $post = $this->createPublishedPost('post-mod');

        Comment::query()->create([
            'commentable_type' => Post::class,
            'commentable_id' => $post->id,
            'author_name' => 'Pending User',
            'author_email' => 'p@example.com',
            'author_ip' => '127.0.0.1',
            'content' => 'Belum disetujui',
            'status' => Comment::STATUS_PENDING,
        ]);

        Livewire::test(CommentSection::class, [
            'commentableType' => Post::class,
            'commentableId' => $post->id,
        ])->assertDontSee('Belum disetujui');
    }

    public function test_approved_comment_visible(): void
    {
        $post = $this->createPublishedPost('post-approved');

        Comment::query()->create([
            'commentable_type' => Post::class,
            'commentable_id' => $post->id,
            'author_name' => 'Approved User',
            'author_email' => 'a@example.com',
            'author_ip' => '127.0.0.1',
            'content' => 'Komentar disetujui',
            'status' => Comment::STATUS_APPROVED,
        ]);

        Livewire::test(CommentSection::class, [
            'commentableType' => Post::class,
            'commentableId' => $post->id,
        ])->assertSee('Komentar disetujui');

        $this->get('/id/blog/post-approved')->assertOk()->assertSee('Komentar disetujui');
    }

    public function test_honeypot_blocks_spam(): void
    {
        Mail::fake();

        $post = $this->createPublishedPost('post-honey');

        Livewire::test(CommentForm::class, [
            'commentableType' => Post::class,
            'commentableId' => $post->id,
        ])
            ->set('authorName', 'Bot')
            ->set('authorEmail', 'bot@example.com')
            ->set('content', 'Promo murah')
            ->set('website', 'http://spam.test')
            ->call('submit')
            ->assertSet('submitted', true);

        $this->assertDatabaseHas('comments', [
            'commentable_id' => $post->id,
            'status' => Comment::STATUS_SPAM,
        ]);

        Mail::assertNothingSent();
    }

    public function test_rate_limit_blocks_flood(): void
    {
        RateLimiter::clear('comment-submit:127.0.0.1');

        $post = $this->createPublishedPost('post-rate');

        for ($i = 0; $i < 3; $i++) {
            Livewire::test(CommentForm::class, [
                'commentableType' => Post::class,
                'commentableId' => $post->id,
            ])
                ->set('authorName', 'Flood ' . $i)
                ->set('authorEmail', 'f' . $i . '@example.com')
                ->set('content', 'Komentar ' . $i)
                ->call('submit')
                ->assertSet('submitted', true);
        }

        Livewire::test(CommentForm::class, [
            'commentableType' => Post::class,
            'commentableId' => $post->id,
        ])
            ->set('authorName', 'Flood blocked')
            ->set('authorEmail', 'block@example.com')
            ->set('content', 'Keempat')
            ->call('submit')
            ->assertSet('submitted', false)
            ->assertSet('errorMessage', 'Terlalu banyak komentar. Coba lagi nanti.');

        $this->assertSame(3, Comment::query()->where('commentable_id', $post->id)->count());
    }

    public function test_nested_comment_max_depth(): void
    {
        $post = $this->createPublishedPost('post-depth');
        $settings = app(CommentSettings::class);
        $settings->max_depth = 3;
        $settings->save();

        $service = app(CommentService::class);

        $level1 = $service->createComment([
            'author_name' => 'L1',
            'author_email' => 'l1@example.com',
            'author_ip' => '127.0.0.1',
            'content' => 'Level 1',
            'force_spam' => false,
        ], $post);
        $service->moderate($level1, Comment::STATUS_APPROVED);

        $level2 = $service->createComment([
            'parent_id' => $level1->id,
            'author_name' => 'L2',
            'author_email' => 'l2@example.com',
            'author_ip' => '127.0.0.1',
            'content' => 'Level 2',
            'force_spam' => false,
        ], $post);
        $service->moderate($level2, Comment::STATUS_APPROVED);

        $level3 = $service->createComment([
            'parent_id' => $level2->id,
            'author_name' => 'L3',
            'author_email' => 'l3@example.com',
            'author_ip' => '127.0.0.1',
            'content' => 'Level 3',
            'force_spam' => false,
        ], $post);
        $service->moderate($level3, Comment::STATUS_APPROVED);

        $this->expectException(\InvalidArgumentException::class);

        $service->createComment([
            'parent_id' => $level3->id,
            'author_name' => 'L4',
            'author_email' => 'l4@example.com',
            'author_ip' => '127.0.0.1',
            'content' => 'Level 4',
            'force_spam' => false,
        ], $post);
    }
}
