<?php

namespace App\Domain\Seo\Services;

use App\Domain\Blog\Category\Models\Category;
use App\Domain\Blog\Post\Models\Post;
use App\Domain\Blog\Series\Models\Series;
use App\Domain\Blog\Tag\Models\Tag;
use App\Domain\Gallery\Album\Models\Album;
use App\Domain\Language\Models\Language;
use App\Domain\Media\Models\Media;
use App\Domain\Page\Models\Page;
use App\Domain\Setting\Settings\GeneralSettings;
use App\Domain\Setting\Settings\SeoSettings;
use Illuminate\Database\Eloquent\Model;

class SeoService
{
    public function __construct(
        private readonly SeoSettings $seoSettings,
        private readonly GeneralSettings $generalSettings,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     * @return array{
     *     title: string,
     *     description: ?string,
     *     keywords: ?string,
     *     canonical: string,
     *     og_type: string,
     *     og_title: string,
     *     og_description: ?string,
     *     og_image: ?string,
     *     og_url: string,
     *     hreflang: list<array{locale: string, url: string}>,
     *     json_ld: list<array<string, mixed>>
     * }
     */
    public function resolveMeta(?object $model, string $locale, array $context = []): array
    {
        $base = $this->defaults($locale);

        $resolved = match (true) {
            $model instanceof Post => $this->metaForPost($model, $locale, $base),
            $model instanceof Page => $this->metaForPage($model, $locale, $base),
            $model instanceof Category => $this->metaForCategory($model, $locale, $base),
            $model instanceof Tag => $this->metaForTag($model, $locale, $base),
            $model instanceof Series => $this->metaForSeries($model, $locale, $base),
            $model instanceof Album => $this->metaForAlbum($model, $locale, $base),
            default => $base,
        };

        if (isset($context['title'])) {
            $resolved['title'] = (string) $context['title'];
            $resolved['og_title'] = (string) $context['title'];
        }

        if (isset($context['description'])) {
            $resolved['description'] = (string) $context['description'];
            $resolved['og_description'] = (string) $context['description'];
        }

        if (isset($context['canonical'])) {
            $resolved['canonical'] = (string) $context['canonical'];
            $resolved['og_url'] = (string) $context['canonical'];
        }

        if (isset($context['og_type'])) {
            $resolved['og_type'] = (string) $context['og_type'];
        }

        if (isset($context['og_image'])) {
            $resolved['og_image'] = (string) $context['og_image'];
        }

        $breadcrumbs = (array) ($context['breadcrumbs'] ?? []);

        if ($breadcrumbs !== []) {
            $resolved['json_ld'][] = $this->breadcrumbListSchema($breadcrumbs);
        }

        if (($context['website_schema'] ?? false) === true) {
            $resolved['json_ld'][] = $this->websiteSchema($locale);
        }

        return $resolved;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(string $locale): array
    {
        $siteName = $this->generalSettings->site_name ?: config('app.name', 'CMS Blog');
        $title = $this->seoSettings->default_meta_title ?: $siteName;
        $description = $this->seoSettings->default_meta_description ?: $this->generalSettings->site_description;
        $canonical = url("/{$locale}");

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => null,
            'canonical' => $canonical,
            'og_type' => 'website',
            'og_title' => $title,
            'og_description' => $description,
            'og_image' => $this->seoSettings->default_og_image,
            'og_url' => $canonical,
            'hreflang' => $this->hreflangForSamePath($locale),
            'json_ld' => [],
        ];
    }

    /**
     * @param  array<string, mixed>  $base
     * @return array<string, mixed>
     */
    protected function metaForPost(Post $post, string $locale, array $base): array
    {
        $translation = $post->translate($locale, false);
        $slug = $translation?->slug;

        $title = $translation?->meta_title ?: $translation?->title ?: $base['title'];
        $description = $translation?->meta_description ?: $translation?->excerpt ?: $base['description'];
        $keywords = $translation?->meta_keywords;
        $canonical = $slug ? url("/{$locale}/blog/{$slug}") : $base['canonical'];

        $ogImage = $this->mediaUrl($post->featuredImage)
            ?? $this->mediaUrlFromId($translation?->og_image_id)
            ?? $base['og_image'];

        $meta = array_merge($base, [
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
            'canonical' => $canonical,
            'og_type' => 'article',
            'og_title' => $title,
            'og_description' => $description,
            'og_image' => $ogImage,
            'og_url' => $canonical,
            'hreflang' => $this->hreflangForModel($post, $locale, fn (string $loc, ?string $s) => $s ? url("/{$loc}/blog/{$s}") : null),
        ]);

        $meta['json_ld'][] = $this->blogPostingSchema($post, $locale, $meta);

        return $meta;
    }

    /**
     * @param  array<string, mixed>  $base
     * @return array<string, mixed>
     */
    protected function metaForPage(Page $page, string $locale, array $base): array
    {
        $translation = $page->translate($locale, false);
        $slug = $translation?->slug;

        $title = $translation?->meta_title ?: $translation?->title ?: $base['title'];
        $description = $translation?->meta_description ?: $base['description'];
        $canonical = $slug ? url("/{$locale}/page/{$slug}") : $base['canonical'];

        if ($page->is_homepage) {
            $canonical = url("/{$locale}");
        }

        return array_merge($base, [
            'title' => $title,
            'description' => $description,
            'canonical' => $canonical,
            'og_title' => $title,
            'og_description' => $description,
            'og_url' => $canonical,
            'hreflang' => $page->is_homepage
                ? $this->hreflangForSamePath($locale)
                : $this->hreflangForModel($page, $locale, fn (string $loc, ?string $s) => $s ? url("/{$loc}/page/{$s}") : null),
        ]);
    }

    /**
     * @param  array<string, mixed>  $base
     * @return array<string, mixed>
     */
    protected function metaForCategory(Category $category, string $locale, array $base): array
    {
        $translation = $category->translate($locale, false);
        $slug = $translation?->slug;
        $title = $translation?->meta_title ?: $translation?->name ?: $base['title'];
        $description = $translation?->meta_description ?: $translation?->description ?: $base['description'];
        $canonical = $slug ? url("/{$locale}/category/{$slug}") : $base['canonical'];

        return array_merge($base, [
            'title' => $title,
            'description' => $description,
            'canonical' => $canonical,
            'og_title' => $title,
            'og_description' => $description,
            'og_url' => $canonical,
            'og_image' => $this->mediaUrl($category->cover) ?? $base['og_image'],
            'hreflang' => $this->hreflangForModel($category, $locale, fn (string $loc, ?string $s) => $s ? url("/{$loc}/category/{$s}") : null),
        ]);
    }

    /**
     * @param  array<string, mixed>  $base
     * @return array<string, mixed>
     */
    protected function metaForTag(Tag $tag, string $locale, array $base): array
    {
        $translation = $tag->translate($locale, false);
        $slug = $translation?->slug;
        $title = $translation?->name ?: $base['title'];
        $canonical = $slug ? url("/{$locale}/tag/{$slug}") : $base['canonical'];

        return array_merge($base, [
            'title' => $title,
            'description' => $base['description'],
            'canonical' => $canonical,
            'og_title' => $title,
            'og_url' => $canonical,
            'hreflang' => $this->hreflangForModel($tag, $locale, fn (string $loc, ?string $s) => $s ? url("/{$loc}/tag/{$s}") : null),
        ]);
    }

    /**
     * @param  array<string, mixed>  $base
     * @return array<string, mixed>
     */
    protected function metaForSeries(Series $series, string $locale, array $base): array
    {
        $translation = $series->translate($locale, false);
        $slug = $translation?->slug;
        $title = $translation?->meta_title ?: $translation?->title ?: $base['title'];
        $description = $translation?->meta_description ?: $translation?->description ?: $base['description'];
        $canonical = $slug ? url("/{$locale}/series/{$slug}") : $base['canonical'];

        return array_merge($base, [
            'title' => $title,
            'description' => $description,
            'canonical' => $canonical,
            'og_title' => $title,
            'og_description' => $description,
            'og_url' => $canonical,
            'og_image' => $this->mediaUrl($series->cover) ?? $base['og_image'],
            'hreflang' => $this->hreflangForModel($series, $locale, fn (string $loc, ?string $s) => $s ? url("/{$loc}/series/{$s}") : null),
        ]);
    }

    /**
     * @param  array<string, mixed>  $base
     * @return array<string, mixed>
     */
    protected function metaForAlbum(Album $album, string $locale, array $base): array
    {
        $translation = $album->translate($locale, false);
        $slug = $translation?->slug;
        $title = $translation?->title ?: $base['title'];
        $description = $translation?->description ?: $base['description'];
        $canonical = $slug ? url("/{$locale}/gallery/{$slug}") : $base['canonical'];

        return array_merge($base, [
            'title' => $title,
            'description' => $description,
            'canonical' => $canonical,
            'og_title' => $title,
            'og_description' => $description,
            'og_url' => $canonical,
            'og_image' => $this->mediaUrl($album->cover) ?? $base['og_image'],
            'hreflang' => $this->hreflangForModel($album, $locale, fn (string $loc, ?string $s) => $s ? url("/{$loc}/gallery/{$s}") : null),
        ]);
    }

    /**
     * @param  callable(string, ?string): ?string  $urlBuilder
     * @return list<array{locale: string, url: string}>
     */
    protected function hreflangForModel(Model $model, string $currentLocale, callable $urlBuilder): array
    {
        $alternates = [];

        foreach (Language::getActive() as $language) {
            $locale = $language->code;
            $slug = $this->translationSlug($model, $locale);

            $url = $urlBuilder($locale, $slug);

            if (blank($url)) {
                continue;
            }

            $alternates[] = [
                'locale' => $locale,
                'url' => $url,
            ];
        }

        if ($alternates === []) {
            return $this->hreflangForSamePath($currentLocale);
        }

        return $alternates;
    }

    /**
     * @return list<array{locale: string, url: string}>
     */
    protected function hreflangForSamePath(string $currentLocale): array
    {
        $path = request()->getPathInfo();
        $segments = array_values(array_filter(explode('/', trim($path, '/'))));

        if ($segments !== [] && Language::getActive()->pluck('code')->contains($segments[0])) {
            array_shift($segments);
        }

        $suffix = $segments === [] ? '' : '/' . implode('/', $segments);

        $alternates = [];

        foreach (Language::getActive() as $language) {
            $alternates[] = [
                'locale' => $language->code,
                'url' => url('/' . $language->code . $suffix),
            ];
        }

        return $alternates;
    }

    protected function translationSlug(Model $model, string $locale): ?string
    {
        if (! method_exists($model, 'translations')) {
            return null;
        }

        $translation = $model->translate($locale, false);

        if ($translation === null) {
            $translation = $model->translations()->where('locale', $locale)->first();
        }

        return $translation?->slug;
    }

    protected function mediaUrl(?Media $media): ?string
    {
        return $media?->getFullUrl();
    }

    protected function mediaUrlFromId(?int $mediaId): ?string
    {
        if ($mediaId === null) {
            return null;
        }

        $media = Media::query()->find($mediaId);

        return $this->mediaUrl($media);
    }

    /**
     * @param  array<string, mixed>  $meta
     * @return array<string, mixed>
     */
    protected function blogPostingSchema(Post $post, string $locale, array $meta): array
    {
        $translation = $post->translate($locale, false);

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $translation?->title,
            'description' => $meta['description'],
            'datePublished' => $post->published_at?->toIso8601String(),
            'dateModified' => $post->updated_at?->toIso8601String(),
            'author' => [
                '@type' => 'Person',
                'name' => $post->user?->name,
            ],
            'image' => $meta['og_image'],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $meta['canonical'],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function websiteSchema(string $locale): array
    {
        $siteName = $this->generalSettings->site_name ?: config('app.name', 'CMS Blog');

        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $siteName,
            'url' => url("/{$locale}"),
        ];
    }

    /**
     * @param  list<array{name: string, url: string}>  $breadcrumbs
     * @return array<string, mixed>
     */
    protected function breadcrumbListSchema(array $breadcrumbs): array
    {
        $items = [];

        foreach ($breadcrumbs as $index => $crumb) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $crumb['name'],
                'item' => $crumb['url'],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }
}
