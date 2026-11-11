<?php

namespace App\Domain\Affiliate\Filament\Concerns;

use App\Domain\Affiliate\Models\AffiliatePlatform;
use App\Domain\Language\Models\Language;

trait SyncsAffiliatePlatformTranslations
{
    /**
     * @param  array<string, mixed>  $data
     */
    protected function syncAffiliatePlatformTranslations(AffiliatePlatform $platform, array $data): void
    {
        foreach (Language::getActive() as $language) {
            $locale = $language->code;
            $bucket = $data['translations'][$locale] ?? null;

            if (! is_array($bucket)) {
                continue;
            }

            $name = trim((string) ($bucket['name'] ?? ''));

            if ($name === '') {
                continue;
            }

            $platform->translateOrNew($locale)->fill(['name' => $name])->save();
        }

        $platform->load('translations');
    }
}
