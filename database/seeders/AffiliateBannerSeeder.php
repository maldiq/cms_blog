<?php

namespace Database\Seeders;

use App\Domain\Affiliate\Models\AffiliateBanner;
use App\Domain\Language\Models\Language;
use App\Domain\Media\Services\MediaUploadService;
use Database\Seeders\Concerns\GeneratesDemoImages;
use Illuminate\Database\Seeder;

class AffiliateBannerSeeder extends Seeder
{
    use GeneratesDemoImages;

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

    /**
     * Palet warna gambar latar slide, mengikuti tema slide.
     *
     * @var array<string, array{0: array{0: int, 1: int, 2: int}, 1: array{0: int, 1: int, 2: int}}>
     */
    private array $themePalettes = [
        'brand' => [[27, 40, 61], [30, 64, 175]],
        'accent' => [[124, 45, 18], [245, 158, 11]],
        'emerald' => [[6, 46, 37], [13, 148, 136]],
        'violet' => [[46, 16, 101], [139, 92, 246]],
    ];

    public function run(): void
    {
        /** @var list<array<string, mixed>> $slides */
        $slides = config('affiliate.hero.fallback_slides', []);

        $uploadService = app(MediaUploadService::class);

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

            if ($banner->image_id === null) {
                $theme = (string) ($slide['theme'] ?? 'brand');
                [$from, $to] = $this->themePalettes[$theme] ?? $this->themePalettes['brand'];
                $imagePath = $this->generateDemoImage(
                    "affiliate-banner-{$index}-{$theme}.jpg",
                    1600,
                    900,
                    $from,
                    $to,
                );
                $banner->update(['image_id' => $uploadService->uploadFromPath($imagePath)->getKey()]);
            }
        }
    }
}
