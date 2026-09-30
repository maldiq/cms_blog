<?php

namespace App\View\Components\Theme\Sections;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ContactSection extends Component
{
    public function render(): View
    {
        return view('theme::components.contact-section');
    }
}
