<?php

namespace Database\Seeders;

use App\Domain\Blog\Category\Models\Category;
use App\Domain\Blog\Category\Models\CategoryTranslation;
use App\Domain\Blog\Post\Models\Post;
use App\Domain\Blog\Series\Models\Series;
use App\Domain\Blog\Tag\Models\Tag;
use App\Domain\User\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        if (CategoryTranslation::query()->where('locale', 'id')->where('slug', 'teknologi')->exists()) {
            return;
        }

        $author = User::query()->where('email', 'admin@admin.com')->first()
            ?? User::factory()->create(['email' => 'author@blog.test', 'is_active' => true]);

        $categories = [
            ['id' => ['Teknologi', 'teknologi'], 'en' => ['Technology', 'technology']],
            ['id' => ['Tutorial', 'tutorial'], 'en' => ['Tutorial', 'tutorial']],
            ['id' => ['Berita', 'berita'], 'en' => ['News', 'news']],
            ['id' => ['Opini', 'opini'], 'en' => ['Opinion', 'opinion']],
            ['id' => ['Review', 'review'], 'en' => ['Review', 'review']],
        ];

        $categoryModels = [];

        foreach ($categories as $index => $item) {
            $category = Category::query()->create([
                'is_active' => true,
                'sort_order' => $index + 1,
            ]);

            $category->translateOrNew('id')->fill([
                'name' => $item['id'][0],
                'slug' => $item['id'][1],
                'description' => 'Kategori '.$item['id'][0],
            ])->save();

            $category->translateOrNew('en')->fill([
                'name' => $item['en'][0],
                'slug' => $item['en'][1],
                'description' => 'Category '.$item['en'][0],
            ])->save();

            $categoryModels[] = $category;
        }

        $tagNames = [
            ['Laravel', 'laravel'],
            ['PHP', 'php'],
            ['JavaScript', 'javascript'],
            ['Tailwind', 'tailwind'],
            ['Filament', 'filament'],
            ['MySQL', 'mysql'],
            ['API', 'api'],
            ['Security', 'security'],
            ['Performance', 'performance'],
            ['DevOps', 'devops'],
        ];

        $tagModels = [];

        foreach ($tagNames as $tagName) {
            $tag = Tag::query()->create(['is_active' => true]);
            $tag->translateOrNew('id')->fill(['name' => $tagName[0], 'slug' => $tagName[1]])->save();
            $tag->translateOrNew('en')->fill(['name' => $tagName[0], 'slug' => $tagName[1]])->save();
            $tagModels[] = $tag;
        }

        $seriesData = [
            ['id' => ['Belajar Laravel', 'belajar-laravel'], 'en' => ['Learn Laravel', 'learn-laravel']],
            ['id' => ['Panduan Filament', 'panduan-filament'], 'en' => ['Filament Guide', 'filament-guide']],
        ];

        $seriesModels = [];

        foreach ($seriesData as $item) {
            $series = Series::query()->create(['is_active' => true]);
            $series->translateOrNew('id')->fill([
                'title' => $item['id'][0],
                'slug' => $item['id'][1],
                'description' => 'Series '.$item['id'][0],
            ])->save();
            $series->translateOrNew('en')->fill([
                'title' => $item['en'][0],
                'slug' => $item['en'][1],
                'description' => 'Series '.$item['en'][0],
            ])->save();
            $seriesModels[] = $series;
        }

        $statuses = [
            Post::STATUS_PUBLISHED,
            Post::STATUS_PUBLISHED,
            Post::STATUS_PUBLISHED,
            Post::STATUS_DRAFT,
            Post::STATUS_REVIEW,
            Post::STATUS_SCHEDULED,
            Post::STATUS_ARCHIVED,
        ];

        for ($i = 1; $i <= 20; $i++) {
            $status = $statuses[$i % count($statuses)];
            $publishedAt = in_array($status, [Post::STATUS_PUBLISHED, Post::STATUS_ARCHIVED], true) ? now()->subDays(21 - $i) : null;

            $post = Post::query()->create([
                'user_id' => $author->id,
                'series_id' => $i <= 8 ? $seriesModels[0]->id : ($i <= 14 ? $seriesModels[1]->id : null),
                'series_order' => $i <= 14 ? ($i % 7) + 1 : null,
                'status' => $status,
                'type' => match ($i % 3) {
                    0 => 'tutorial',
                    1 => 'news',
                    default => 'article',
                },
                'published_at' => $publishedAt,
                'is_featured' => $i % 5 === 0,
                'allow_comment' => true,
            ]);

            $titleId = "Artikel Contoh {$i}";
            $titleEn = "Sample Article {$i}";
            $content = '<p>'.str_repeat('Konten artikel panjang untuk reading time. ', 120).'</p>';

            $post->translateOrNew('id')->fill([
                'title' => $titleId,
                'slug' => Str::slug($titleId),
                'excerpt' => 'Ringkasan artikel '.$i,
                'content' => $content,
                'meta_title' => $titleId,
                'meta_description' => 'Meta artikel '.$i,
            ])->save();

            $post->translateOrNew('en')->fill([
                'title' => $titleEn,
                'slug' => Str::slug($titleEn),
                'excerpt' => 'Excerpt for article '.$i,
                'content' => $content,
                'meta_title' => $titleEn,
                'meta_description' => 'Meta for article '.$i,
            ])->save();

            $post->load('translations');
            $post->recalculateReadingTime();
            $post->saveQuietly();

            $post->categories()->sync([
                $categoryModels[$i % count($categoryModels)]->id,
                $categoryModels[($i + 1) % count($categoryModels)]->id,
            ]);

            $post->tags()->sync([
                $tagModels[$i % count($tagModels)]->id,
                $tagModels[($i + 2) % count($tagModels)]->id,
            ]);
        }
    }
}
