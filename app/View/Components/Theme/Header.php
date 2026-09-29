<?php

namespace App\View\Components\Theme;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Header extends Component
{
    public function render(): View
    {
        return view('theme::layouts.partials.header');
    }
}
