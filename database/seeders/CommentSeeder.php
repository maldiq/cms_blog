<?php

namespace Database\Seeders;

use App\Domain\Blog\Post\Models\Post;
use App\Domain\Comment\Models\Comment;
use App\Domain\User\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $posts = Post::query()->limit(8)->get();
        $user = User::query()->first();

        if ($posts->isEmpty()) {
            return;
        }

        $statuses = [
            Comment::STATUS_PENDING,
            Comment::STATUS_APPROVED,
            Comment::STATUS_APPROVED,
            Comment::STATUS_SPAM,
            Comment::STATUS_TRASH,
        ];

        for ($i = 1; $i <= 20; $i++) {
            $post = $posts->random();
            $status = Arr::random($statuses);
            $isReply = $i > 5 && random_int(0, 1) === 1;

            $parent = null;

            if ($isReply) {
                $parent = Comment::query()
                    ->where('commentable_type', Post::class)
                    ->where('commentable_id', $post->id)
                    ->whereNull('parent_id')
                    ->inRandomOrder()
                    ->first();
            }

            Comment::query()->create([
                'commentable_type' => Post::class,
                'commentable_id' => $post->id,
                'parent_id' => $parent?->id,
                'user_id' => $user && random_int(0, 1) === 1 ? $user->id : null,
                'author_name' => $user && random_int(0, 1) === 1 ? $user->name : 'Pengunjung ' . $i,
                'author_email' => 'guest' . $i . '@example.com',
                'author_url' => random_int(0, 1) === 1 ? 'https://example.com' : null,
                'author_ip' => '127.0.0.1',
                'user_agent' => 'Seeder',
                'content' => 'Komentar contoh #' . $i . ' pada post ID ' . $post->id . '.',
                'status' => $status,
                'is_pinned' => $i === 6,
            ]);
        }
    }
}
