<?php

namespace App\View\Components\Theme;

use App\Domain\Setting\Settings\SocialSettings;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Footer extends Component
{
    /**
     * @var Collection<int, array{key: string, url: string}>
     */
    public Collection $socialLinks;

    /**
     * @var list<array<string, mixed>>
     */
    public array $footerColumns;

    public function __construct()
    {
        $social = app(SocialSettings::class);

        $this->socialLinks = collect([
            'facebook' => $social->facebook,
            'twitter' => $social->twitter,
            'instagram' => $social->instagram,
            'youtube' => $social->youtube,
            'linkedin' => $social->linkedin,
            'tiktok' => $social->tiktok,
        ])
            ->filter(fn (?string $url): bool => filled($url))
            ->map(fn (string $url, string $key): array => [
                'key' => $key,
                'url' => $url,
            ])
            ->values();

        $columns = theme('footer.columns');

        $this->footerColumns = is_array($columns) ? $columns : [];
    }

    public function render(): View
    {
        return view('theme::layouts.partials.footer');
    }
}
