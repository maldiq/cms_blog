<?php

namespace App\View\Components\Storefront;

use App\Domain\Affiliate\Models\AffiliateCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Footer extends Component
{
    /**
     * @var Collection<int, AffiliateCategory>
     */
    public Collection $categories;

    public string $siteName;

    public ?string $siteDescription;

    /**
     * @var array<string, string>
     */
    public array $socials;

    public function __construct()
    {
        $locale = app()->getLocale();

        $this->categories = AffiliateCategory::query()
            ->where('is_active', true)
            ->whereHas('products', fn (Builder $query) => $query
                ->where('is_active', true)
                ->whereHas('translations', fn (Builder $query) => $query->where('locale', $locale)))
            ->with(['translations' => fn ($query) => $query->where('locale', $locale)])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit(6)
            ->get();

        $this->siteName = (string) setting('site_name', config('app.name', 'Affiliate Store'));
        $this->siteDescription = setting('site_description');

        $this->socials = collect([
            'facebook' => setting('facebook'),
            'instagram' => setting('instagram'),
            'tiktok' => setting('tiktok'),
            'youtube' => setting('youtube'),
            'twitter' => setting('twitter'),
            'linkedin' => setting('linkedin'),
        ])
            ->filter(fn ($url): bool => is_string($url) && filter_var($url, FILTER_VALIDATE_URL) !== false)
            ->all();
    }

    public function render(): View
    {
        return view('components.storefront.footer');
    }
}
