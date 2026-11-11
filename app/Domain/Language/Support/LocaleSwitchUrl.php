<?php

namespace App\Domain\Language\Support;

use App\Domain\Affiliate\Models\AffiliateCategory;
use App\Domain\Affiliate\Models\AffiliateProduct;
use App\Domain\Language\Models\Language;
use Illuminate\Http\Request;

class LocaleSwitchUrl
{
    /**
     * URL halaman yang sama dengan locale diganti (untuk switcher header).
     */
    public static function forLocale(string $targetLocale, ?Request $request = null): string
    {
        $request ??= request();

        $segments = $request->segments();

        if (
            isset($segments[0])
            && Language::query()->where('code', $segments[0])->exists()
        ) {
            $segments[0] = $targetLocale;
        } else {
            array_unshift($segments, $targetLocale);
        }

        if ($request->routeIs('products.show')) {
            $segments = self::localizedProductSegments($segments, $request, $targetLocale);
        }

        if ($request->routeIs('products.category', 'products.category.intl')) {
            $segments = self::localizedCategorySegments($segments, $request, $targetLocale);
        }

        $path = '/'.implode('/', $segments);

        $query = $request->getQueryString();

        return $query !== null && $query !== '' ? $path.'?'.$query : $path;
    }

    /**
     * Kategori memakai slug per locale dan segmen path kategori/category.
     *
     * @param  list<string>  $segments
     * @return list<string>
     */
    private static function localizedCategorySegments(array $segments, Request $request, string $targetLocale): array
    {
        $currentLocale = app()->getLocale();
        $currentSlug = (string) $request->route('slug');

        if ($currentSlug === '') {
            return $segments;
        }

        $category = AffiliateCategory::query()
            ->whereHas('translations', fn ($query) => $query
                ->where('locale', $currentLocale)
                ->where('slug', $currentSlug))
            ->with('translations')
            ->first();

        if ($category === null) {
            return $segments;
        }

        $targetSlug = $category->translate($targetLocale, false)?->slug;

        if (blank($targetSlug)) {
            return [$targetLocale, 'products'];
        }

        return [
            $targetLocale,
            $targetLocale === 'id' ? 'kategori' : 'category',
            (string) $targetSlug,
        ];
    }

    /**
     * Produk memakai slug per locale, jadi segmen slug perlu diterjemahkan.
     *
     * @param  list<string>  $segments
     * @return list<string>
     */
    private static function localizedProductSegments(array $segments, Request $request, string $targetLocale): array
    {
        $currentLocale = app()->getLocale();
        $currentSlug = (string) $request->route('slug');

        if ($currentSlug === '') {
            return $segments;
        }

        $product = AffiliateProduct::query()
            ->whereHas('translations', fn ($query) => $query
                ->where('locale', $currentLocale)
                ->where('slug', $currentSlug))
            ->with('translations')
            ->first();

        $targetSlug = $product?->translate($targetLocale, false)?->slug;

        if ($product === null) {
            return $segments;
        }

        if (blank($targetSlug)) {
            // Produk belum diterjemahkan: arahkan ke katalog locale tujuan, bukan 404.
            return [$targetLocale, 'products'];
        }

        return [$targetLocale, 'products', (string) $targetSlug];
    }
}
