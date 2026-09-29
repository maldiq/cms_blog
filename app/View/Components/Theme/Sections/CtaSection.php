<?php

namespace App\View\Components\Theme\Sections;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CtaSection extends Component
{
    public ?string $backgroundUrl;

    public ?string $title;

    public ?string $subtitle;

    public ?string $ctaLabel;

    public ?string $ctaUrl;

    public function __construct()
    {
        $this->backgroundUrl = media_url(theme('cta.background_image_id'));
        $this->title = theme_locale('cta.title');
        $this->subtitle = theme_locale('cta.subtitle');
        $this->ctaLabel = theme_locale('cta.cta_label');
        $this->ctaUrl = theme('cta.cta_url');
    }

    public function render(): View
    {
        return view('theme::components.cta-section');
    }
}
