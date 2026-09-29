<?php

namespace App\Domain\Page\Http\Controllers;

use App\Domain\Page\Services\PageService;
use Illuminate\Contracts\View\View;

class PageController
{
    public function __construct(
        private readonly PageService $pageService,
    ) {}

    public function home(string $locale): View
    {
        app()->setLocale($locale);

        $page = $this->pageService->getHomepage($locale);

        if ($page !== null) {
            return view('page.show', [
                'page' => $page,
                'locale' => $locale,
                'seo' => $page,
                'websiteSchema' => true,
                'breadcrumbs' => [
                    ['name' => setting('site_name', config('app.name')), 'url' => url("/{$locale}")],
                    ['name' => (string) $page->translate($locale, false)?->title, 'url' => url()->current()],
                ],
            ]);
        }

        return view('welcome', [
            'locale' => $locale,
            'websiteSchema' => true,
        ]);
    }

    public function show(string $locale, string $slug): View
    {
        app()->setLocale($locale);

        $page = $this->pageService->getBySlug($slug, $locale);
        abort_if($page === null, 404);

        return view('page.show', [
            'page' => $page,
            'locale' => $locale,
            'seo' => $page,
            'breadcrumbs' => [
                ['name' => setting('site_name', config('app.name')), 'url' => url("/{$locale}")],
                ['name' => (string) $page->translate($locale, false)?->title, 'url' => url()->current()],
            ],
        ]);
    }
}
