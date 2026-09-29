<?php

namespace App\Domain\Blog\Post\Http\Controllers;

use App\Domain\Blog\Post\Services\PostService;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function __construct(protected PostService $postService) {}

    public function index(string $locale): View
    {
        app()->setLocale($locale);

        return view('blog.index', [
            'locale' => $locale,
            'seoContext' => [
                'title' => 'Blog',
                'canonical' => route('blog.index', ['locale' => $locale]),
            ],
        ]);
    }

    public function show(string $locale, string $slug): View
    {
        app()->setLocale($locale);

        $post = $this->postService->getBySlug($slug, $locale);

        abort_if($post === null, 404);

        $this->postService->incrementViewCount($post);

        $related = $this->postService->getRelated($post);

        $translation = $post->translate($locale, false);

        return view('blog.show', [
            'post' => $post,
            'related' => $related,
            'locale' => $locale,
            'seo' => $post,
            'breadcrumbs' => [
                ['name' => setting('site_name', config('app.name')), 'url' => url("/{$locale}")],
                ['name' => 'Blog', 'url' => route('blog.index', ['locale' => $locale])],
                ['name' => (string) $translation?->title, 'url' => url()->current()],
            ],
        ]);
    }

    public function category(string $locale, string $slug, Request $request): View
    {
        app()->setLocale($locale);

        $category = $this->postService->findCategoryBySlug($slug, $locale);
        abort_if($category === null, 404);

        $posts = $this->postService->getPublished($locale, ['category' => $slug]);

        return view('blog.index', [
            'posts' => $posts,
            'locale' => $locale,
            'category' => $category,
            'seo' => $category,
        ]);
    }

    public function tag(string $locale, string $slug): View
    {
        app()->setLocale($locale);

        $tag = $this->postService->findTagBySlug($slug, $locale);
        abort_if($tag === null, 404);

        $posts = $this->postService->getPublished($locale, ['tag' => $slug]);

        return view('blog.index', [
            'posts' => $posts,
            'locale' => $locale,
            'tag' => $tag,
            'seo' => $tag,
        ]);
    }

    public function series(string $locale, string $slug): View
    {
        app()->setLocale($locale);

        $series = $this->postService->findSeriesBySlug($slug, $locale);
        abort_if($series === null, 404);

        $posts = $this->postService->getBySeries($series, $locale);

        return view('blog.series', [
            'series' => $series,
            'posts' => $posts,
            'locale' => $locale,
            'seo' => $series,
        ]);
    }

    public function seriesPost(string $locale, string $slug, string $postSlug): View
    {
        app()->setLocale($locale);

        $series = $this->postService->findSeriesBySlug($slug, $locale);
        abort_if($series === null, 404);

        $posts = $this->postService->getBySeries($series, $locale);
        $post = $this->postService->getBySlug($postSlug, $locale);

        abort_if($post === null || $post->series_id !== $series->id, 404);

        $this->postService->incrementViewCount($post);

        $currentIndex = $posts->search(fn ($item) => $item->id === $post->id);
        $previous = $currentIndex !== false && $currentIndex > 0 ? $posts[$currentIndex - 1] : null;
        $next = $currentIndex !== false && $currentIndex < $posts->count() - 1 ? $posts[$currentIndex + 1] : null;

        $translation = $post->translate($locale, false);

        return view('blog.show', [
            'post' => $post,
            'related' => collect(),
            'locale' => $locale,
            'series' => $series,
            'seriesPosts' => $posts,
            'previousSeriesPost' => $previous,
            'nextSeriesPost' => $next,
            'seo' => $post,
            'breadcrumbs' => [
                ['name' => setting('site_name', config('app.name')), 'url' => url("/{$locale}")],
                ['name' => 'Blog', 'url' => route('blog.index', ['locale' => $locale])],
                ['name' => (string) $series->translate($locale, false)?->title, 'url' => route('blog.series', ['locale' => $locale, 'slug' => $series->translate($locale, false)?->slug])],
                ['name' => (string) $translation?->title, 'url' => url()->current()],
            ],
        ]);
    }
}
