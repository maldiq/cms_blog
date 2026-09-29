<?php

namespace App\View\Components\Theme\Sections;

use App\Support\Theme\ThemeValue;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
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
        $this->title = theme_locale('faq.title');
        $this->subtitle = theme_locale('faq.subtitle');

        $rawItems = theme('faq.items');
        $items = is_array($rawItems) ? $rawItems : [];

        $this->items = collect($items)->map(function (mixed $item): array {
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
