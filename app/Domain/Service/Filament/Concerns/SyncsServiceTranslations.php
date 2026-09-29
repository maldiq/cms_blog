<?php

namespace App\Domain\Service\Filament\Concerns;

use App\Domain\Language\Models\Language;
use App\Domain\Service\Models\Service;

trait SyncsServiceTranslations
{
    /**
     * @param  array<string, mixed>  $data
     */
    protected function syncServiceTranslations(Service $service, array $data): void
    {
        foreach (Language::getActive() as $language) {
            $locale = $language->code;
            $bucket = $data['translations'][$locale] ?? null;

            if (! is_array($bucket)) {
                continue;
            }

            $service->translateOrNew($locale)->fill([
                'title' => $bucket['title'] ?? '',
                'slug' => $bucket['slug'] ?? null,
                'excerpt' => $bucket['excerpt'] ?? null,
                'content' => $bucket['content'] ?? null,
                'meta_title' => $bucket['meta_title'] ?? null,
                'meta_description' => $bucket['meta_description'] ?? null,
                'meta_keywords' => $bucket['meta_keywords'] ?? null,
            ])->save();
        }
    }
}
