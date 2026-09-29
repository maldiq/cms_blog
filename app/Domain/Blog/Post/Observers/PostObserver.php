<?php

namespace App\Domain\Blog\Post\Observers;

use App\Domain\Blog\Post\Models\Post;
use App\Domain\Seo\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PostObserver
{
    public function saving(Post $post): void
    {
        if ($post->isDirty('status') && $post->status === Post::STATUS_PUBLISHED && $post->published_at === null) {
            $post->published_at = now();
        }

        $this->syncReadingTime($post);
    }

    public function saved(Post $post): void
    {
        $this->clearPostCaches($post);
        SitemapController::forgetCache();
    }

    public function deleted(Post $post): void
    {
        $this->clearPostCaches($post);
        SitemapController::forgetCache();
    }

    protected function syncReadingTime(Post $post): void
    {
        $content = '';

        foreach (['id', 'en'] as $locale) {
            $translation = $post->translate($locale, false);
            if ($translation !== null && filled($translation->content)) {
                $content = (string) $translation->content;
                break;
            }
        }

        if ($content === '' && $post->exists) {
            $post->load('translations');
            $content = (string) ($post->translations->first()?->content ?? '');
        }

        $plain = trim(preg_replace('/\s+/', ' ', strip_tags($content)) ?? '');
        $words = Str::wordCount($plain);
        $post->reading_time = max(1, (int) ceil($words / 200));
    }

    protected function clearPostCaches(Post $post): void
    {
        Cache::forget('post.related.'.$post->id);

        foreach (['id', 'en'] as $locale) {
            $translation = $post->translate($locale, false);
            if ($translation !== null) {
                Cache::forget("post.slug.{$locale}.{$translation->slug}");
            }
        }
    }
}
