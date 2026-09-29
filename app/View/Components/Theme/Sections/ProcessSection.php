<?php

namespace App\View\Components\Theme\Sections;

use App\Support\Theme\ThemeValue;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class ProcessSection extends Component
{
    public ?string $title;

    public ?string $subtitle;

    /**
     * @var Collection<int, array{number: ?string, title: ?string, description: ?string}>
     */
    public Collection $steps;

    public function __construct()
    {
        $this->title = theme_locale('process.title');
        $this->subtitle = theme_locale('process.subtitle');

        $rawSteps = theme('process.steps');
        $items = is_array($rawSteps) ? $rawSteps : [];

        $this->steps = collect($items)->map(function (mixed $item): array {
            if (! is_array($item)) {
                return [
                    'number' => null,
                    'title' => null,
                    'description' => null,
                ];
            }

            return [
                'number' => filled($item['number'] ?? null) ? (string) $item['number'] : null,
                'title' => ThemeValue::localize($item['title'] ?? null),
                'description' => ThemeValue::localize($item['description'] ?? null),
            ];
        })->filter(fn (array $step): bool => filled($step['number']) || filled($step['title']) || filled($step['description']));
    }

    public function render(): View
    {
        return view('theme::components.process-section');
    }
}
