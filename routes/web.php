<?php

use App\Domain\Blog\Post\Http\Controllers\BlogController;
use App\Domain\Gallery\Album\Http\Controllers\GalleryController;
use App\Domain\Page\Http\Controllers\PageController;
use App\Domain\Portfolio\Http\Controllers\PortfolioController;
use App\Domain\Service\Http\Controllers\ServiceController;
use App\Domain\Theme\Http\Controllers\ThemePageController;
use App\Domain\Language\Models\Language;
use App\Domain\Seo\Http\Controllers\RobotsController;
use App\Domain\Seo\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::redirect('/admin/media', '/kelola/media');
Route::redirect('/admin/menus', '/kelola/menus');
Route::redirect('/admin/posts', '/kelola/posts');
Route::redirect('/admin/albums', '/kelola/albums');
Route::redirect('/admin/comments', '/kelola/comments');
Route::redirect('/admin/pages', '/kelola/pages');
Route::redirect('/admin', '/kelola');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', RobotsController::class)->name('robots');

Route::get('/', function () {
    $defaultCode = Language::getDefault()?->code ?? 'id';

    return redirect('/' . $defaultCode);
});

Route::prefix('{locale}')
    ->where(['locale' => '[A-Za-z]{2,5}'])
    ->group(function () {
        Route::get('/', [PageController::class, 'home'])->name('home');

        Route::get('/page/{slug}', [PageController::class, 'show'])->name('page.show');

        Route::get('/about', [ThemePageController::class, 'about'])->name('about');

        Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
        Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show');

        Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio.index');
        Route::get('/portfolio/{slug}', [PortfolioController::class, 'show'])->name('portfolio.show');

        Route::get('/team', [ThemePageController::class, 'team'])->name('team.index');
        Route::get('/testimonials', [ThemePageController::class, 'testimonials'])->name('testimonial.index');
        Route::get('/pricing', [ThemePageController::class, 'pricing'])->name('pricing');
        Route::get('/faq', [ThemePageController::class, 'faq'])->name('faq');
        Route::get('/contact', [ThemePageController::class, 'contact'])->name('contact');

        Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
        Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
        Route::get('/category/{slug}', [BlogController::class, 'category'])->name('blog.category');
        Route::get('/tag/{slug}', [BlogController::class, 'tag'])->name('blog.tag');
        Route::get('/series/{slug}', [BlogController::class, 'series'])->name('blog.series');
        Route::get('/series/{slug}/{post_slug}', [BlogController::class, 'seriesPost'])->name('blog.series.post');

        Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
        Route::get('/gallery/{slug}', [GalleryController::class, 'show'])->name('gallery.show');
    });
