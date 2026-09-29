<?php

namespace App\Domain\Theme\Http\Controllers;

use App\Domain\Team\Models\Team;
use App\Domain\Testimonial\Models\Testimonial;
use App\Domain\Theme\Services\ThemeService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View as ViewFacade;

class ThemePageController
{
    public function about(string $locale): View
    {
        return $this->render($locale, 'about', [
            'seoContext' => [
                'title' => theme_locale('about.title'),
                'canonical' => route('about', ['locale' => $locale]),
            ],
        ]);
    }

    public function team(string $locale): View
    {
        app()->setLocale($locale);

        $teams = Team::query()
            ->with(['translations', 'photo'])
            ->active()
            ->ordered()
            ->get();

        return $this->render($locale, 'team', [
            'teams' => $teams,
            'seoContext' => [
                'title' => theme_locale('team.title'),
                'canonical' => route('team.index', ['locale' => $locale]),
            ],
        ]);
    }

    public function testimonials(string $locale, Request $request): View
    {
        app()->setLocale($locale);

        $query = Testimonial::query()
            ->with(['translations', 'photo'])
            ->active()
            ->ordered();

        if ($request->filled('rating')) {
            $query->where('rating', (int) $request->input('rating'));
        }

        $testimonials = $query->get();

        return $this->render($locale, 'testimonial', [
            'testimonials' => $testimonials,
            'seoContext' => [
                'title' => theme_locale('testimonial.title'),
                'canonical' => route('testimonial.index', ['locale' => $locale]),
            ],
        ]);
    }

    public function pricing(string $locale): View
    {
        return $this->render($locale, 'pricing', [
            'seoContext' => [
                'title' => theme_locale('pricing.title'),
                'canonical' => route('pricing', ['locale' => $locale]),
            ],
        ]);
    }

    public function faq(string $locale): View
    {
        return $this->render($locale, 'faq', [
            'seoContext' => [
                'title' => theme_locale('faq.title'),
                'canonical' => route('faq', ['locale' => $locale]),
            ],
        ]);
    }

    public function contact(string $locale): View
    {
        return $this->render($locale, 'contact', [
            'seoContext' => [
                'title' => theme_locale('contact.email') ?? setting('site_name'),
                'canonical' => route('contact', ['locale' => $locale]),
            ],
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function render(string $locale, string $page, array $data = []): View
    {
        app()->setLocale($locale);
        app(ThemeService::class)->applyTheme();

        abort_unless(ViewFacade::exists("theme::pages.{$page}"), 404);

        return view("theme::pages.{$page}", array_merge([
            'locale' => $locale,
        ], $data));
    }
}
