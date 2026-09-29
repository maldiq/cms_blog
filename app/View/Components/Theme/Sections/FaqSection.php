<?php

namespace App\View\Components\Theme\Sections;

use App\Support\Theme\HomeSectionCache;
use App\Support\Theme\ThemeValue;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;

class FaqSection extends Component
{
    public ?string $title;

    public ?string $subtitle;

    /**
     * @var Collection<int, array{question: ?string, answer: ?string}>
     */
    public Collection $items;

    public function __construct()
    {
        $locale = app()->getLocale();
        $this->title = theme_locale('faq.title');
        $this->subtitle = theme_locale('faq.subtitle');

        /** @var Collection<int, array{question: ?string, answer: ?string}> $items */
        $items = Cache::remember(
            HomeSectionCache::key('faq', $locale),
            HomeSectionCache::TTL_SECONDS,
            fn (): Collection => $this->buildItems(),
        );

        $this->items = $items;
    }

    /**
     * @return Collection<int, array{question: ?string, answer: ?string}>
     */
    protected function buildItems(): Collection
    {
        $rawItems = theme('faq.items');
        $items = is_array($rawItems) ? $rawItems : [];

        return collect($items)->map(function (mixed $item): array {
            if (! is_array($item)) {
                return ['question' => null, 'answer' => null];
            }

            return [
                'question' => ThemeValue::localize($item['question'] ?? null),
                'answer' => ThemeValue::localize($item['answer'] ?? null),
            ];
        })->filter(fn (array $item): bool => filled($item['question']) || filled($item['answer']));
    }

    public function render(): View
    {
        return view('theme::components.faq-section');
    }
}
