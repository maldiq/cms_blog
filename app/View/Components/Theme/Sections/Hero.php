<?php

namespace App\View\Components\Theme\Sections;

use App\Support\Theme\ThemeValue;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Hero extends Component
{
    public ?string $backgroundUrl;

    public ?string $primaryColor;

    public ?string $title;

    public ?string $subtitle;

    public ?string $ctaLabel;

    public ?string $ctaUrl;

    /**
     * @var Collection<int, array{icon: ?string, text: ?string}>
     */
    public Collection $features;

    public function __construct()
    {
        $this->backgroundUrl = media_url(theme('hero.background_image_id'));
        $this->primaryColor = theme('branding.primary_color');
        $this->title = theme_locale('hero.title');
        $this->subtitle = theme_locale('hero.subtitle');
        $this->ctaLabel = theme_locale('hero.cta_label');
        $this->ctaUrl = theme('hero.cta_url');

        $rawFeatures = theme('hero.features');
        $items = is_array($rawFeatures) ? $rawFeatures : [];

        $this->features = collect($items)->map(function (mixed $item): array {
            if (! is_array($item)) {
                return ['icon' => null, 'text' => null];
            }

            $text = ThemeValue::localize($item['title'] ?? null)
                ?? ThemeValue::localize($item['description'] ?? null);

            return [
                'icon' => filled($item['icon'] ?? null) ? (string) $item['icon'] : null,
                'text' => filled($text) ? (string) $text : null,
            ];
        })->filter(fn (array $feature): bool => filled($feature['icon']) || filled($feature['text']));
    }

    public function render(): View
    {
        return view('theme::components.hero');
    }
}
