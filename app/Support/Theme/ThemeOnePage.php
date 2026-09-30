<?php

namespace App\Support\Theme;

use App\Domain\Theme\Models\Theme;

class ThemeOnePage
{
    /** @var list<string> */
    public const SLUGS = ['purplelanding'];

    public static function isActive(): bool
    {
        $slug = Theme::current()?->slug;

        return $slug !== null && in_array($slug, self::SLUGS, true);
    }

    /**
     * Redirect route name → fragment id di homepage.
     *
     * @return array<string, string>
     */
    public static function routeFragmentMap(): array
    {
        return [
            'about' => 'about',
            'services.index' => 'solutions',
            'services.show' => 'solutions',
            'portfolio.index' => 'about',
            'portfolio.show' => 'about',
            'team.index' => 'about',
            'testimonial.index' => 'reviews',
            'pricing' => 'pricing',
            'faq' => 'faq',
            'contact' => 'contact',
            'blog.index' => 'blog',
            'blog.category' => 'blog',
            'blog.tag' => 'blog',
            'blog.series' => 'blog',
            'blog.series.post' => 'blog',
            'gallery.index' => 'about',
            'gallery.show' => 'about',
            'page.show' => 'top',
        ];
    }

    public static function homeUrl(string $locale, ?string $fragment = null): string
    {
        $url = url('/'.$locale);

        if (filled($fragment)) {
            return $url.'#'.$fragment;
        }

        return $url;
    }
}
