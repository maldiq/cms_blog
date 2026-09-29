<?php

namespace App\View\Components\Theme;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PageHero extends Component
{
    public function __construct(
        public ?string $title = null,
        public ?string $subtitle = null,
    ) {}

    public function render(): View
    {
        return view('theme::components.page-hero');
    }
}
