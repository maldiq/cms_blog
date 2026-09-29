<?php

namespace App\View\Components\Theme\Sections;

use App\Domain\Service\Models\Service;
use App\Domain\Service\Services\ServiceCatalogService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class ServicesSection extends Component
{
    public ?string $title;

    public ?string $subtitle;

    public ?string $ctaLabel;

    /**
     * @var Collection<int, Service>
     */
    public Collection $services;

    public function __construct(ServiceCatalogService $serviceCatalogService)
    {
        $this->title = theme_locale('services.title');
        $this->subtitle = theme_locale('services.subtitle');
        $this->ctaLabel = theme_locale('services.cta_label');
        $this->services = $serviceCatalogService->listActiveForHome(6);
    }

    public function render(): View
    {
        return view('theme::components.services-section');
    }
}
