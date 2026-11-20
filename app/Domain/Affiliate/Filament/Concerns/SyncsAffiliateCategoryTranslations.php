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
                'intro' => $bucket['intro'] ?? null,
                'meta_title' => $bucket['meta_title'] ?? null,
                'meta_description' => $bucket['meta_description'] ?? null,
                'faq' => $this->normalizeFaq($bucket['faq'] ?? null),
            ])->save();
        }

        $category->load('translations');
    }

    /**
     * @return list<array{question: string, answer: string}>|null
     */
    private function normalizeFaq(mixed $faq): ?array
    {
        if (! is_array($faq)) {
            return null;
        }

        $items = [];

        foreach ($faq as $item) {
            if (! is_array($item)) {
                continue;
            }

            $question = trim((string) ($item['question'] ?? ''));
            $answer = trim((string) ($item['answer'] ?? ''));

            if ($question === '' || $answer === '') {
                continue;
            }

            $items[] = ['question' => $question, 'answer' => $answer];
        }

        return $items === [] ? null : $items;
    }
}
