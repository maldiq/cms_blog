<?php

namespace App\View\Components\Theme\Sections;

use App\Support\Theme\ThemeValue;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class PricingSection extends Component
{
    public ?string $title;

    public ?string $subtitle;

    /**
     * @var Collection<int, array<string, mixed>>
     */
    public Collection $plans;

    public function __construct()
    {
        $this->title = theme_locale('pricing.title');
        $this->subtitle = theme_locale('pricing.subtitle');

        $rawPlans = theme('pricing.plans');
        $items = is_array($rawPlans) ? $rawPlans : [];

        $this->plans = collect($items)->map(function (mixed $plan): array {
            if (! is_array($plan)) {
                return [];
            }

            $features = collect($plan['features'] ?? [])->map(function (mixed $feature): ?string {
                if (! is_array($feature)) {
                    return null;
                }

                $text = ThemeValue::localize($feature['text'] ?? null);

                return filled($text) ? (string) $text : null;
            })->filter()->values()->all();

            return [
                'name' => ThemeValue::localize($plan['name'] ?? null),
                'price' => filled($plan['price'] ?? null) ? (string) $plan['price'] : null,
                'period' => filled($plan['period'] ?? null) ? (string) $plan['period'] : null,
                'cta_label' => ThemeValue::localize($plan['cta_label'] ?? null),
                'cta_url' => filled($plan['cta_url'] ?? null) ? (string) $plan['cta_url'] : null,
                'is_popular' => (bool) ($plan['is_popular'] ?? false),
                'features' => $features,
            ];
        })->filter(fn (array $plan): bool => filled($plan['name'] ?? null));
    }

    public function render(): View
    {
        return view('theme::components.pricing-section');
    }
}
