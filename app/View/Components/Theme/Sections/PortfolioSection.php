<?php

namespace App\View\Components\Theme\Sections;

use App\Domain\Portfolio\Services\PortfolioCatalogService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class PortfolioSection extends Component
{
    public ?string $title;

    public ?string $subtitle;

    public ?string $ctaLabel;

    /**
     * @var Collection<int, \App\Domain\Portfolio\Models\Portfolio>
     */
    public Collection $portfolios;

    public function __construct(PortfolioCatalogService $portfolioCatalogService)
    {
        $this->title = theme_locale('portfolio.title');
        $this->subtitle = theme_locale('portfolio.subtitle');
        $this->ctaLabel = theme_locale('portfolio.cta_label');
        $this->portfolios = $portfolioCatalogService->listFeaturedForHome(6);
    }

    public function render(): View
    {
        return view('theme::components.portfolio-section');
    }
}
