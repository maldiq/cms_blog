<?php

namespace App\View\Components\Theme\Sections;

use App\Support\Theme\ThemeValue;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class AboutSection extends Component
{
    public ?string $imageUrl;

    public ?string $title;

    public ?string $subtitle;

    public ?string $content;

    public string $aboutUrl;

    /**
     * @var Collection<int, array{text: ?string}>
     */
    public Collection $points;

    public function __construct()
    {
        $locale = app()->getLocale();

        $this->imageUrl = media_url(theme('about.image_id'));
        $this->title = theme_locale('about.title');
        $this->subtitle = theme_locale('about.subtitle');
        $this->content = theme_locale('about.content');
        $this->aboutUrl = url('/' . $locale . '/about');

        $rawPoints = theme('about.points');
        $items = is_array($rawPoints) ? $rawPoints : [];

        $this->points = collect($items)->map(function (mixed $item): array {
            if (! is_array($item)) {
                return ['text' => null];
            }

            $text = ThemeValue::localize($item['text'] ?? null);

            return [
                'text' => filled($text) ? (string) $text : null,
            ];
        })->filter(fn (array $point): bool => filled($point['text']));
    }

    public function render(): View
    {
        return view('theme::components.about-section');
    }
}
