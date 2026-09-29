<?php

namespace App\View\Components\Theme\Sections;

use App\Support\Theme\ThemeValue;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class StatsSection extends Component
{
    public ?string $primaryColor;

    /**
     * @var Collection<int, array{number: ?string, label: ?string, suffix: ?string, numeric_target: float}>
     */
    public Collection $items;

    public function __construct()
    {
        $this->primaryColor = theme('branding.primary_color');

        $rawItems = theme('stats.items');
        $items = is_array($rawItems) ? $rawItems : [];

        $this->items = collect($items)->map(function (mixed $item): array {
            if (! is_array($item)) {
                return [
                    'number' => null,
                    'label' => null,
                    'suffix' => null,
                    'numeric_target' => 0.0,
                ];
            }

            $number = filled($item['number'] ?? null) ? (string) $item['number'] : null;
            $suffix = filled($item['suffix'] ?? null) ? (string) $item['suffix'] : null;
            $label = ThemeValue::localize($item['label'] ?? null);

            return [
                'number' => $number,
                'label' => filled($label) ? (string) $label : null,
                'suffix' => $suffix,
                'numeric_target' => self::parseNumericTarget($number),
            ];
        })->filter(fn (array $stat): bool => filled($stat['number']) || filled($stat['label']));
    }

    public function render(): View
    {
        return view('theme::components.stats-section');
    }

    protected static function parseNumericTarget(?string $number): float
    {
        if ($number === null) {
            return 0.0;
        }

        if (preg_match('/-?\d+(\.\d+)?/', $number, $matches) !== 1) {
            return 0.0;
        }

        return (float) $matches[0];
    }
}
