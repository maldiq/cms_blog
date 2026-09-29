<?php

use App\Domain\Blog\Post\Http\Controllers\BlogController;
use App\Domain\Gallery\Album\Http\Controllers\GalleryController;
use App\Domain\Language\Models\Language;
use Illuminate\Support\Facades\Route;

Route::redirect('/admin/media', '/kelola/media');
Route::redirect('/admin/menus', '/kelola/menus');
Route::redirect('/admin/posts', '/kelola/posts');
Route::redirect('/admin/albums', '/kelola/albums');
Route::redirect('/admin/comments', '/kelola/comments');
Route::redirect('/admin', '/kelola');

Route::get('/sitemap.xml', function () {
    $path = storage_path('app/sitemap.xml');

    abort_unless(is_file($path), 404);

    return response()->file($path, ['Content-Type' => 'application/xml']);
})->name('sitemap');

Route::get('/', function () {
    $defaultCode = Language::getDefault()?->code ?? 'id';

    return redirect('/' . $defaultCode);
});

Route::prefix('{locale}')
    ->where(['locale' => '[A-Za-z]{2,5}'])
    ->group(function () {
        Route::get('/', function () {
            return view('welcome');
        })->name('home');

        Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
        Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
        Route::get('/category/{slug}', [BlogController::class, 'category'])->name('blog.category');
        Route::get('/tag/{slug}', [BlogController::class, 'tag'])->name('blog.tag');
        Route::get('/series/{slug}', [BlogController::class, 'series'])->name('blog.series');
        Route::get('/series/{slug}/{post_slug}', [BlogController::class, 'seriesPost'])->name('blog.series.post');

        Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
        Route::get('/gallery/{slug}', [GalleryController::class, 'show'])->name('gallery.show');
    });
