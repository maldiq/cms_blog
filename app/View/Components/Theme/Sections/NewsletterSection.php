<?php

namespace App\View\Components\Theme\Sections;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class NewsletterSection extends Component
{
    public ?string $title;

    public ?string $subtitle;

    public ?string $placeholder;

    public ?string $buttonLabel;

    public function __construct()
    {
        $this->title = theme_locale('newsletter.title');
        $this->subtitle = theme_locale('newsletter.subtitle');
        $this->placeholder = theme_locale('newsletter.placeholder');
        $this->buttonLabel = theme_locale('newsletter.button_label');
    }

    public function render(): View
    {
        return view('theme::components.newsletter-section');
    }
}
