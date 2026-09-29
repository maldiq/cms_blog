<?php

namespace App\Domain\Seo\Services;

use App\Domain\Blog\Category\Models\Category;
use App\Domain\Blog\Post\Models\Post;
use App\Domain\Blog\Series\Models\Series;
use App\Domain\Blog\Tag\Models\Tag;
use App\Domain\Gallery\Album\Models\Album;
use App\Domain\Language\Models\Language;
use App\Domain\Page\Models\Page;
use App\Domain\Portfolio\Models\Portfolio;
use App\Domain\Service\Models\Service;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapService
{
    public function buildXml(): string
    {
        $sitemap = Sitemap::create();

        foreach (Language::getActive() as $language) {
            $locale = $language->code;

            $sitemap->add(
                Url::create(url("/{$locale}"))
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                    ->setPriority(1.0)
            );

            $sitemap->add(
                Url::create(url("/{$locale}/blog"))
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                    ->setPriority(0.9)
            );

            $sitemap->add(
                Url::create(url("/{$locale}/gallery"))
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(0.7)
            );

            $this->addThemeStaticRoutes($sitemap, $locale);

            $this->addPosts($sitemap, $locale);
            $this->addPages($sitemap, $locale);
            $this->addServices($sitemap, $locale);
            $this->addPortfolios($sitemap, $locale);
            $this->addSeries($sitemap, $locale);
            $this->addCategories($sitemap, $locale);
            $this->addTags($sitemap, $locale);
            $this->addAlbums($sitemap, $locale);
        }

        return $sitemap->render();
    }

    protected function addThemeStaticRoutes(Sitemap $sitemap, string $locale): void
    {
        $routes = [
            ['path' => 'about', 'priority' => 0.75, 'frequency' => Url::CHANGE_FREQUENCY_MONTHLY],
            ['path' => 'services', 'priority' => 0.85, 'frequency' => Url::CHANGE_FREQUENCY_WEEKLY],
            ['path' => 'portfolio', 'priority' => 0.8, 'frequency' => Url::CHANGE_FREQUENCY_WEEKLY],
            ['path' => 'team', 'priority' => 0.7, 'frequency' => Url::CHANGE_FREQUENCY_MONTHLY],
            ['path' => 'testimonials', 'priority' => 0.65, 'frequency' => Url::CHANGE_FREQUENCY_MONTHLY],
            ['path' => 'pricing', 'priority' => 0.7, 'frequency' => Url::CHANGE_FREQUENCY_MONTHLY],
            ['path' => 'faq', 'priority' => 0.65, 'frequency' => Url::CHANGE_FREQUENCY_MONTHLY],
            ['path' => 'contact', 'priority' => 0.75, 'frequency' => Url::CHANGE_FREQUENCY_MONTHLY],
        ];

        foreach ($routes as $route) {
            $sitemap->add(
                Url::create(url("/{$locale}/{$route['path']}"))
                    ->setChangeFrequency($route['frequency'])
                    ->setPriority($route['priority'])
            );
        }
    }

    protected function addServices(Sitemap $sitemap, string $locale): void
    {
        Service::query()
            ->active()
            ->whereHas('translations', fn ($query) => $query->where('locale', $locale))
            ->with(['translations' => fn ($query) => $query->where('locale', $locale)])
            ->orderBy('id')
            ->chunk(100, function ($services) use ($sitemap, $locale): void {
                foreach ($services as $service) {
                    $slug = $service->translate($locale, false)?->slug;

                    if (blank($slug)) {
                        continue;
                    }

                    $sitemap->add(
                        Url::create(url("/{$locale}/services/{$slug}"))
                            ->setLastModificationDate($service->updated_at)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.75)
                    );
                }
            });
    }

    protected function addPortfolios(Sitemap $sitemap, string $locale): void
    {
        Portfolio::query()
            ->active()
            ->whereHas('translations', fn ($query) => $query->where('locale', $locale))
            ->with(['translations' => fn ($query) => $query->where('locale', $locale)])
            ->orderBy('id')
            ->chunk(100, function ($portfolios) use ($sitemap, $locale): void {
                foreach ($portfolios as $portfolio) {
                    $slug = $portfolio->translate($locale, false)?->slug;

                    if (blank($slug)) {
                        continue;
                    }

                    $sitemap->add(
                        Url::create(url("/{$locale}/portfolio/{$slug}"))
                            ->setLastModificationDate($portfolio->updated_at)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                            ->setPriority(0.7)
                    );
                }
            });
    }

    protected function addPosts(Sitemap $sitemap, string $locale): void
    {
        Post::query()
            ->published()
            ->whereHas('translations', fn ($query) => $query->where('locale', $locale))
            ->with(['translations' => fn ($query) => $query->where('locale', $locale)])
            ->orderBy('id')
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

    protected function addPages(Sitemap $sitemap, string $locale): void
    {
        Page::query()
            ->published()
            ->whereHas('translations', fn ($query) => $query->where('locale', $locale))
            ->with(['translations' => fn ($query) => $query->where('locale', $locale)])
            ->orderBy('id')
            ->chunk(100, function ($pages) use ($sitemap, $locale): void {
                foreach ($pages as $page) {
                    $slug = $page->translate($locale, false)?->slug;

                    if (blank($slug)) {
                        continue;
                    }

                    $sitemap->add(
                        Url::create(url("/{$locale}/page/{$slug}"))
                            ->setLastModificationDate($page->updated_at)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                            ->setPriority(0.7)
                    );
                }
            });
    }

    protected function addSeries(Sitemap $sitemap, string $locale): void
    {
        Series::query()
            ->where('is_active', true)
            ->whereHas('translations', fn ($query) => $query->where('locale', $locale))
            ->with(['translations' => fn ($query) => $query->where('locale', $locale)])
            ->orderBy('id')
            ->chunk(100, function ($seriesList) use ($sitemap, $locale): void {
                foreach ($seriesList as $series) {
                    $slug = $series->translate($locale, false)?->slug;

                    if (blank($slug)) {
                        continue;
                    }

                    $sitemap->add(
                        Url::create(url("/{$locale}/series/{$slug}"))
                            ->setLastModificationDate($series->updated_at)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.75)
                    );
                }
            });
    }

    protected function addCategories(Sitemap $sitemap, string $locale): void
    {
        Category::query()
            ->where('is_active', true)
            ->whereHas('translations', fn ($query) => $query->where('locale', $locale))
            ->with(['translations' => fn ($query) => $query->where('locale', $locale)])
            ->orderBy('id')
            ->chunk(100, function ($categories) use ($sitemap, $locale): void {
                foreach ($categories as $category) {
                    $slug = $category->translate($locale, false)?->slug;

                    if (blank($slug)) {
                        continue;
                    }

                    $sitemap->add(
                        Url::create(url("/{$locale}/category/{$slug}"))
                            ->setLastModificationDate($category->updated_at)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.65)
                    );
                }
            });
    }

    protected function addTags(Sitemap $sitemap, string $locale): void
    {
        Tag::query()
            ->where('is_active', true)
            ->whereHas('translations', fn ($query) => $query->where('locale', $locale))
            ->with(['translations' => fn ($query) => $query->where('locale', $locale)])
            ->orderBy('id')
            ->chunk(100, function ($tags) use ($sitemap, $locale): void {
                foreach ($tags as $tag) {
                    $slug = $tag->translate($locale, false)?->slug;

                    if (blank($slug)) {
                        continue;
                    }

                    $sitemap->add(
                        Url::create(url("/{$locale}/tag/{$slug}"))
                            ->setLastModificationDate($tag->updated_at)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.6)
                    );
                }
            });
    }

    protected function addAlbums(Sitemap $sitemap, string $locale): void
    {
        Album::query()
            ->where('is_active', true)
            ->whereHas('translations', fn ($query) => $query->where('locale', $locale))
            ->with(['translations' => fn ($query) => $query->where('locale', $locale)])
            ->orderBy('id')
            ->chunk(100, function ($albums) use ($sitemap, $locale): void {
                foreach ($albums as $album) {
                    $slug = $album->translate($locale, false)?->slug;

                    if (blank($slug)) {
                        continue;
                    }

                    $sitemap->add(
                        Url::create(url("/{$locale}/gallery/{$slug}"))
                            ->setLastModificationDate($album->updated_at)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                            ->setPriority(0.65)
                    );
                }
            });
    }
}
