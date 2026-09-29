<?php

namespace App\Domain\Portfolio\Http\Controllers;

use App\Domain\Portfolio\Services\PortfolioCatalogService;
use Illuminate\Contracts\View\View;

class PortfolioController
{
    public function __construct(
        private readonly PortfolioCatalogService $portfolioCatalogService,
    ) {}

    public function index(string $locale): View
    {
        app()->setLocale($locale);

        return view('portfolio.index', [
            'locale' => $locale,
            'portfolios' => $this->portfolioCatalogService->listActive(),
            'seoContext' => [
                'title' => theme_locale('portfolio.title'),
                'canonical' => route('portfolio.index', ['locale' => $locale]),
            ],
        ]);
    }

    public function show(string $locale, string $slug): View
    {
        app()->setLocale($locale);

        $portfolio = $this->portfolioCatalogService->findBySlug($slug, $locale);

        abort_if($portfolio === null, 404);

        $translation = $portfolio->translate($locale, false);

        return view('portfolio.show', [
            'portfolio' => $portfolio,
            'locale' => $locale,
            'translation' => $translation,
            'seo' => $portfolio,
            'breadcrumbs' => [
                ['name' => setting('site_name'), 'url' => url("/{$locale}")],
                ['name' => (string) theme_locale('portfolio.title'), 'url' => route('portfolio.index', ['locale' => $locale])],
                ['name' => (string) $translation?->title, 'url' => url()->current()],
            ],
        ]);
    }
}
