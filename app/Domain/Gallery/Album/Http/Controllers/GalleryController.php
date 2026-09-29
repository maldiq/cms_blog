<?php

namespace App\Domain\Gallery\Album\Http\Controllers;

use App\Domain\Gallery\Album\Services\AlbumService;
use Illuminate\Contracts\View\View;

class GalleryController
{
    public function __construct(
        private readonly AlbumService $albumService,
    ) {}

    public function index(string $locale): View
    {
        app()->setLocale($locale);

        $albums = $this->albumService->getActiveList($locale);

        return view('gallery.index', [
            'albums' => $albums,
            'locale' => $locale,
        ]);
    }

    public function show(string $locale, string $slug): View
    {
        app()->setLocale($locale);

        $album = $this->albumService->getBySlug($slug, $locale);
        abort_if($album === null, 404);

        return view('gallery.show', [
            'album' => $album,
            'locale' => $locale,
        ]);
    }
}
