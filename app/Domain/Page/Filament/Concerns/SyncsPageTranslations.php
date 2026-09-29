<?php

namespace App\Domain\Page\Filament\Concerns;

use App\Domain\Language\Models\Language;
use App\Domain\Page\Models\Page;

trait SyncsPageTranslations
{
    /**
     * @param  array<string, mixed>  $data
     */
    protected function syncPageTranslations(Page $page, array $data): void
    {
        foreach (Language::getActive() as $language) {
            $locale = $language->code;
            $bucket = $data['translations'][$locale] ?? null;

            if (! is_array($bucket)) {
                continue;
            }

            $page->translateOrNew($locale)->fill([
                'title' => $bucket['title'] ?? '',
                'slug' => $bucket['slug'] ?? null,
                'content' => $bucket['content'] ?? null,
                'meta_title' => $bucket['meta_title'] ?? null,
                'meta_description' => $bucket['meta_description'] ?? null,
            ])->save();
        }
    }
}
