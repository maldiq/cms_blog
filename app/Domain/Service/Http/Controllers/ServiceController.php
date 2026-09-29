<?php

namespace App\Domain\Service\Http\Controllers;

use App\Domain\Service\Services\ServiceCatalogService;
use Illuminate\Contracts\View\View;

class ServiceController
{
    public function __construct(
        private readonly ServiceCatalogService $serviceCatalogService,
    ) {}

    public function index(string $locale): View
    {
        app()->setLocale($locale);

        $services = $this->serviceCatalogService->listActiveForHome(100);

        return view('service.index', [
            'locale' => $locale,
            'services' => $services,
            'seoContext' => [
                'title' => theme_locale('services.title'),
                'canonical' => route('service.index', ['locale' => $locale]),
            ],
        ]);
    }

    public function show(string $locale, string $slug): View
    {
        app()->setLocale($locale);

        $service = $this->serviceCatalogService->findBySlug($slug, $locale);

        abort_if($service === null, 404);

        $translation = $service->translate($locale, false);

        return view('service.show', [
            'service' => $service,
            'locale' => $locale,
            'translation' => $translation,
            'seo' => $service,
            'breadcrumbs' => [
                ['name' => setting('site_name'), 'url' => url("/{$locale}")],
                ['name' => (string) theme_locale('services.title'), 'url' => route('service.index', ['locale' => $locale])],
                ['name' => (string) $translation?->title, 'url' => url()->current()],
            ],
        ]);
    }
}
