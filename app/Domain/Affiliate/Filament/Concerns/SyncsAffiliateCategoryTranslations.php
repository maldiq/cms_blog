<?php

namespace App\Domain\Affiliate\Filament\Concerns;

use App\Domain\Affiliate\Models\AffiliateCategory;
use App\Domain\Language\Models\Language;

trait SyncsAffiliateCategoryTranslations
{
    /**
     * @param  array<string, mixed>  $data
     */
    protected function syncAffiliateCategoryTranslations(AffiliateCategory $category, array $data): void
    {
        foreach (Language::getActive() as $language) {
            $locale = $language->code;
            $bucket = $data['translations'][$locale] ?? null;

            if (! is_array($bucket)) {
                continue;
            }

            $name = trim((string) ($bucket['name'] ?? ''));
            $slug = trim((string) ($bucket['slug'] ?? ''));

            if ($name === '' && $slug === '') {
                continue;
            }

            $category->translateOrNew($locale)->fill([
                'name' => $name,
                'slug' => $slug !== '' ? $slug : null,
            ])->save();
        }

        $category->load('translations');
    }
}
