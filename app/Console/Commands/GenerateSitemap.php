<?php

namespace App\Console\Commands;

use App\Domain\Blog\Post\Models\Post;
use App\Domain\Language\Models\Language;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Generate sitemap.xml untuk halaman blog publik';

    public function handle(): int
    {
        $sitemap = Sitemap::create();

        foreach (Language::getActive() as $language) {
            $locale = $language->code;

            $sitemap->add(Url::create(url("/{$locale}/blog"))->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));

            Post::query()
                ->published()
                ->whereHas('translations', fn ($q) => $q->where('locale', $locale))
                ->with(['translations' => fn ($q) => $q->where('locale', $locale)])
                ->chunk(100, function ($posts) use ($sitemap, $locale): void {
                    foreach ($posts as $post) {
                        $slug = $post->translate($locale, false)?->slug;

                        if (blank($slug)) {
                            continue;
                        }

                        $sitemap->add(
                            Url::create(url("/{$locale}/blog/{$slug}"))
                                ->setLastModificationDate($post->updated_at)
                                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                                ->setPriority(0.8)
                        );
                    }
                });
        }

        $path = storage_path('app/sitemap.xml');
        $sitemap->writeToFile($path);

        $this->info("Sitemap ditulis ke {$path}");

        return self::SUCCESS;
    }
}
