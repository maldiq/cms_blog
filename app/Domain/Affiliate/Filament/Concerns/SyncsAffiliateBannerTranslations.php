<?php

namespace App\Domain\Affiliate\Filament\Concerns;

use App\Domain\Affiliate\Models\AffiliateBanner;
use App\Domain\Language\Models\Language;

trait SyncsAffiliateBannerTranslations
{
    /**
     * @param  array<string, mixed>  $data
     */
    protected function syncAffiliateBannerTranslations(AffiliateBanner $banner, array $data): void
    {
        foreach (Language::getActive() as $language) {
            $locale = $language->code;
            $bucket = $data['translations'][$locale] ?? null;

            if (! is_array($bucket)) {
                continue;
            }

            $badge = trim((string) ($bucket['badge'] ?? ''));
            $title = trim((string) ($bucket['title'] ?? ''));
            $subtitle = trim((string) ($bucket['subtitle'] ?? ''));
            $ctaLabel = trim((string) ($bucket['cta_label'] ?? ''));

            if ($badge === '' && $title === '' && $subtitle === '' && $ctaLabel === '') {
                continue;
            }

            $banner->translateOrNew($locale)->fill([
                'badge' => $badge !== '' ? $badge : null,
                'title' => $title !== '' ? $title : null,
                'subtitle' => $subtitle !== '' ? $subtitle : null,
                'cta_label' => $ctaLabel !== '' ? $ctaLabel : null,
            ])->save();
        }

        $banner->load('translations');
    }
}
