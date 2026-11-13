<?php

namespace App\Domain\Portfolio\Filament\Concerns;

use App\Domain\Language\Models\Language;
use App\Domain\Portfolio\Models\Portfolio;
use Illuminate\Support\Arr;

trait SyncsPortfolioTranslations
{
    /**
     * @param  array<string, mixed>  $data
     */
    protected function syncPortfolioTranslations(Portfolio $portfolio, array $data): void
    {
        foreach (Language::getActive() as $language) {
            $locale = $language->code;
            $bucket = $data['translations'][$locale] ?? null;

            if (! is_array($bucket)) {
                continue;
            }

            $portfolio->translateOrNew($locale)->fill(Arr::only($bucket, [
                'title',
                'slug',
                'excerpt',
                'content',
                'meta_title',
                'meta_description',
                'meta_keywords',
            ]))->save();
        }
    }
}
