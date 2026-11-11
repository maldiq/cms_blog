<?php

namespace Database\Seeders;

use App\Domain\Affiliate\Models\AffiliateBanner;
use App\Domain\Language\Models\Language;
use Illuminate\Database\Seeder;

class AffiliateBannerSeeder extends Seeder
{
    /**
     * @var array<int, array<string, string>>
     */
    private array $englishSlides = [
        0 => [
            'badge' => 'Weekly picks',
            'title' => 'Shop smart, save every day.',
            'subtitle' => 'Handpicked products from Shopee, TikTok Shop, Amazon, and more — checked one by one before entering the catalog.',
            'cta_label' => 'Explore Products',
        ],
        1 => [
            'badge' => 'Marketplace promo',
            'title' => 'Compare prices without switching tabs.',
            'subtitle' => 'Find the best offers, original prices, and official marketplace links on a single page.',
            'cta_label' => 'View Featured Products',
        ],
        2 => [
            'badge' => 'New this week',
            'title' => 'Fresh finds, straight from the store.',
            'subtitle' => 'The catalog is updated daily. Bookmark this page and discover new products before they trend.',
            'cta_label' => 'View New Products',
        ],
    ];

    public function run(): void
    {
        /** @var list<array<string, mixed>> $slides */
        $slides = config('affiliate.hero.fallback_slides', []);

        $locales = Language::getActive()->pluck('code')->all();

        if ($locales === []) {
            $locales = [config('app.locale', 'id')];
        }

        foreach ($slides as $index => $slide) {
            $title = (string) ($slide['title'] ?? '');

            $banner = AffiliateBanner::query()
                ->whereHas('translations', fn ($query) => $query->where('title', $title))
                ->first();

            $banner ??= AffiliateBanner::query()->create([
                'theme' => $slide['theme'],
                'link_url' => $slide['link'],
                'is_active' => true,
                'sort_order' => $index,
            ]);

            foreach ($locales as $locale) {
                if ($banner->translations()->where('locale', $locale)->exists()) {
                    continue;
                }

                $copy = $locale === 'en'
                    ? ($this->englishSlides[$index] ?? [
                        'badge' => $slide['badge'],
                        'title' => $title,
                        'subtitle' => $slide['subtitle'],
                        'cta_label' => $slide['cta_label'],
                    ])
                    : [
                        'badge' => $slide['badge'],
                        'title' => $title,
                        'subtitle' => $slide['subtitle'],
                        'cta_label' => $slide['cta_label'],
                    ];

                $banner->translateOrNew($locale)->fill([
                    'badge' => $copy['badge'],
                    'title' => $copy['title'],
                    'subtitle' => $copy['subtitle'],
                    'cta_label' => $copy['cta_label'],
                ])->save();
            }
        }
    }
}
