<?php

namespace App\View\Components\Storefront;

use App\Domain\Affiliate\Models\AffiliateCategory;
use App\Domain\Affiliate\Models\AffiliatePlatform;
use App\Domain\Affiliate\Models\AffiliateProduct;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Header extends Component
{
    /**
     * @var Collection<int, AffiliateCategory>
     */
    public Collection $categories;

    public int $productCount;

    public int $platformCount;

    public ?string $logoUrl;

    public string $siteName;

    public function __construct()
    {
        $locale = app()->getLocale();

        $this->categories = AffiliateCategory::query()
            ->where('is_active', true)
            ->with(['translations' => fn ($query) => $query->where('locale', $locale)])
            ->withCount(['products' => fn (Builder $query) => $query
                ->where('is_active', true)
                ->whereHas('translations', fn (Builder $query) => $query->where('locale', $locale))])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $this->productCount = AffiliateProduct::query()
            ->where('is_active', true)
            ->whereHas('translations', fn (Builder $query) => $query->where('locale', $locale))
            ->count();
        $this->platformCount = AffiliatePlatform::query()->where('is_active', true)->count();
        $this->logoUrl = media_url(setting('site_logo'));
        $this->siteName = (string) setting('site_name', config('app.name', 'Affiliate Store'));
    }

    public function render(): View
    {
        return view('components.storefront.header');
    }
}
