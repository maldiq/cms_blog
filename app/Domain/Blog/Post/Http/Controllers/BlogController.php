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

        return view('blog.index', compact('locale'));
    }

    public function show(string $locale, string $slug): View
    {
        app()->setLocale($locale);

        $post = $this->postService->getBySlug($slug, $locale);

        abort_if($post === null, 404);

        $this->postService->incrementViewCount($post);

        $related = $this->postService->getRelated($post);

        return view('blog.show', compact('post', 'related', 'locale'));
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
        ]);
    }

    public function tag(string $locale, string $slug): View
    {
        app()->setLocale($locale);

        $tag = $this->postService->findTagBySlug($slug, $locale);
        abort_if($tag === null, 404);

        $posts = $this->postService->getPublished($locale, ['tag' => $slug]);

        return view('blog.index', compact('posts', 'locale', 'tag'));
    }

    public function series(string $locale, string $slug): View
    {
        app()->setLocale($locale);

        $series = $this->postService->findSeriesBySlug($slug, $locale);
        abort_if($series === null, 404);

        $posts = $this->postService->getBySeries($series, $locale);

        return view('blog.series', compact('series', 'posts', 'locale'));
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

        return view('blog.show', [
            'post' => $post,
            'related' => collect(),
            'locale' => $locale,
            'series' => $series,
            'seriesPosts' => $posts,
            'previousSeriesPost' => $previous,
            'nextSeriesPost' => $next,
        ]);
    }
}
