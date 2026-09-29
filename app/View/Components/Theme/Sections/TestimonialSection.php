<?php

namespace App\View\Components\Theme\Sections;

use App\Domain\Testimonial\Models\Testimonial;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class TestimonialSection extends Component
{
    public ?string $title;

    public ?string $subtitle;

    /**
     * @var Collection<int, Testimonial>
     */
    public Collection $testimonials;

    public function __construct()
    {
        $this->title = theme_locale('testimonial.title');
        $this->subtitle = theme_locale('testimonial.subtitle');

        $this->testimonials = Testimonial::query()
            ->with(['translations', 'photo'])
            ->active()
            ->ordered()
            ->limit(9)
            ->get();
    }

    public function render(): View
    {
        return view('theme::components.testimonial-section');
    }
}
