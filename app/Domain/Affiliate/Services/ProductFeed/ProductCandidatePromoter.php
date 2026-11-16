<?php

namespace App\Domain\Affiliate\Services\ProductFeed;

use App\Domain\Affiliate\Models\AffiliateProduct;
use App\Domain\Affiliate\Models\AffiliateProductCandidate;
use App\Domain\Affiliate\Models\AffiliateProductTranslation;
use App\Domain\Language\Models\Language;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ProductCandidatePromoter
{
    /**
     * Ubah kandidat menjadi produk katalog. Produk selalu dibuat nonaktif
     * kecuali admin memilih untuk langsung menayangkannya, dan gambar tetap
     * diisi manual agar tidak menautkan aset marketplace secara langsung.
     */
    public function promote(
        AffiliateProductCandidate $candidate,
        int $categoryId,
        int $platformId,
        bool $isActive = false,
    ): AffiliateProduct {
        if ($candidate->status === AffiliateProductCandidate::STATUS_IMPORTED) {
            throw new InvalidArgumentException('Kandidat ini sudah diimpor menjadi produk.');
        }

        $url = $candidate->bestUrl();

        if ($url === null || ! $this->isHttpUrl($url)) {
            throw new InvalidArgumentException('Kandidat belum punya tautan produk yang valid (http/https).');
        }

        $product = AffiliateProduct::query()->create([
            'affiliate_category_id' => $categoryId,
            'affiliate_platform_id' => $platformId,
            'affiliate_url' => $url,
            'price' => $candidate->price,
            'original_price' => $candidate->original_price,
            'is_featured' => false,
            'is_active' => $isActive,
        ]);

        $locales = Language::getActive()->pluck('code')->all() ?: [config('app.locale', 'id')];

        foreach (array_unique($locales) as $locale) {
            $product->translateOrNew($locale)->fill([
                'title' => $candidate->title,
                'slug' => $this->uniqueSlug($candidate->title, $locale),
            ])->save();
        }

        $candidate->forceFill([
            'status' => AffiliateProductCandidate::STATUS_IMPORTED,
            'imported_product_id' => $product->getKey(),
            'curated_at' => now(),
        ])->save();

        return $product;
    }

    private function isHttpUrl(string $url): bool
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        return in_array(strtolower((string) parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true);
    }

    /**
     * Slug unik per locale, karena tabel terjemahan memakai unique(slug, locale).
     */
    private function uniqueSlug(string $title, string $locale): string
    {
        $base = Str::slug($title) ?: 'produk';
        $slug = $base;
        $suffix = 2;

        while (AffiliateProductTranslation::query()
            ->where('locale', $locale)
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
