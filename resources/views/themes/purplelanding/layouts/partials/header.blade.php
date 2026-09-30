@php
    $homeUrl = url('/' . app()->getLocale());
    $logoUrl = media_url(theme('branding.logo_id')) ?? media_url(theme('branding.logo_dark_id'));
    $siteName = setting('site_name');
    $ctaLabel = theme_locale('header.cta_label') ?? __('messages.contact_us');
    $nav = [
        ['id' => 'Solusi', 'en' => 'Solutions', 'url' => '#solutions'],
        ['id' => 'Tentang', 'en' => 'About', 'url' => '#about'],
        ['id' => 'Harga', 'en' => 'Pricing', 'url' => '#pricing'],
        ['id' => 'Ulasan', 'en' => 'Reviews', 'url' => '#reviews'],
        ['id' => 'Blog', 'en' => 'Blog', 'url' => '#blog'],
        ['id' => 'FAQ', 'en' => 'FAQ', 'url' => '#faq'],
        ['id' => 'Hubungi', 'en' => "Let's Talk", 'url' => '#contact'],
    ];
    $locale = app()->getLocale();
@endphp

<header
    x-data="{ scrolled: false, mobileOpen: false }"
    x-init="scrolled = window.scrollY > 20"
    @scroll.window="scrolled = window.scrollY > 20"
    class="sticky top-0 z-40 scroll-mt-0 transition-all duration-300"
    :class="scrolled ? 'border-b border-violet-100 bg-white/95 shadow-sm backdrop-blur-md' : 'bg-white/90 backdrop-blur-sm'"
>
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 lg:px-6">
        <a href="#top" class="flex shrink-0 items-center gap-2">
            @if (filled($logoUrl))
                <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="h-10 w-auto max-w-[180px] object-contain">
            @elseif (filled($siteName))
                <span class="text-lg font-bold text-violet-700">{{ $siteName }}</span>
            @endif
        </a>

        <nav class="hidden items-center gap-1 lg:flex" aria-label="header">
            @foreach ($nav as $item)
                <a
                    href="{{ $item['url'] }}"
                    class="rounded-lg px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-violet-50 hover:text-violet-700"
                >
                    {{ $item[$locale] ?? $item['en'] }}
                </a>
            @endforeach
        </nav>

        <div class="hidden items-center gap-3 lg:flex">
            <x-locale-switcher />
            <a
                href="#contact"
                class="inline-flex rounded-full bg-violet-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-violet-600/20 transition hover:bg-violet-700"
            >
                {{ $ctaLabel }}
            </a>
        </div>

        <button
            type="button"
            class="inline-flex rounded-lg p-2 text-gray-700 hover:bg-violet-50 lg:hidden"
            @click="mobileOpen = ! mobileOpen"
            :aria-expanded="mobileOpen.toString()"
            aria-controls="mobile-nav-purple"
        >
            <span class="sr-only">{{ __('messages.toggle_menu') }}</span>
            <svg x-show="! mobileOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg x-show="mobileOpen" x-cloak class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div id="mobile-nav-purple" x-show="mobileOpen" x-cloak x-transition class="border-t border-violet-100 bg-white px-4 py-4 lg:hidden">
        <nav class="flex flex-col gap-1">
            @foreach ($nav as $item)
                <a
                    href="{{ $item['url'] }}"
                    class="rounded-lg px-3 py-2.5 text-sm font-medium text-gray-800 hover:bg-violet-50"
                    @click="mobileOpen = false"
                >
                    {{ $item[$locale] ?? $item['en'] }}
                </a>
            @endforeach
        </nav>
        <div class="mt-4 flex flex-wrap items-center gap-4">
            <x-locale-switcher />
            <a href="#contact" class="rounded-full bg-violet-600 px-5 py-2.5 text-sm font-semibold text-white">{{ $ctaLabel }}</a>
        </div>
    </div>
</header>
