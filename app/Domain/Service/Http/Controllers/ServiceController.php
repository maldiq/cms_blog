<?php

namespace App\Domain\Service\Http\Controllers;

use App\Domain\Service\Services\ServiceCatalogService;
use App\Domain\Theme\Services\ThemeService;
use Illuminate\Contracts\View\View;

class ServiceController
{
    public function __construct(
        private readonly ServiceCatalogService $serviceCatalogService,
    ) {}

    public function index(string $locale): View
    {
        app()->setLocale($locale);
        app(ThemeService::class)->applyTheme();

        return view('theme::pages.services', [
            'locale' => $locale,
            'services' => $this->serviceCatalogService->paginateActive(12),
            'seoContext' => [
                'title' => theme_locale('services.title'),
                'canonical' => route('services.index', ['locale' => $locale]),
            ],
        ]);
    }

    public function show(string $locale, string $slug): View
    {
        app()->setLocale($locale);

        $service = $this->serviceCatalogService->findBySlug($slug, $locale);

        abort_if($service === null, 404);

        $translation = $service->translate($locale, false);

        app(ThemeService::class)->applyTheme();

        return view('theme::pages.service-detail', [
            'service' => $service,
            'locale' => $locale,
            'translation' => $translation,
            'relatedServices' => $this->serviceCatalogService->relatedServices($service),
            'seo' => $service,
            'breadcrumbs' => [
                ['name' => setting('site_name'), 'url' => url("/{$locale}")],
                ['name' => (string) theme_locale('services.title'), 'url' => route('services.index', ['locale' => $locale])],
                ['name' => (string) $translation?->title, 'url' => url()->current()],
            ],
        ]);
    }
}
