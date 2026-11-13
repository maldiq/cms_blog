<?php

namespace App\Domain\Affiliate\Filament\Concerns;

use App\Domain\Affiliate\Models\AffiliateProduct;
use App\Domain\Language\Models\Language;

trait SyncsAffiliateProductTranslations
{
    /**
     * @param  array<string, mixed>  $data
     */
    protected function syncAffiliateProductTranslations(AffiliateProduct $product, array $data): void
    {
        foreach (Language::getActive() as $language) {
            $locale = $language->code;
            $bucket = $data['translations'][$locale] ?? null;

            if (! is_array($bucket)) {
                continue;
            }

            $title = trim((string) ($bucket['title'] ?? ''));
            $slug = trim((string) ($bucket['slug'] ?? ''));

            if ($title === '' && $slug === '') {
                continue;
            }

            $product->translateOrNew($locale)->fill([
                'title' => $title,
                'slug' => $slug !== '' ? $slug : null,
                'excerpt' => $bucket['excerpt'] ?? null,
                'description' => $bucket['description'] ?? null,
            ])->save();
        }

        $product->load('translations');
    }
}
