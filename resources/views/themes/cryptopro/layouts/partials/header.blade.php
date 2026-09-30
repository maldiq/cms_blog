@php
    $homeUrl = url('/' . app()->getLocale());
    $logoUrl = media_url(theme('branding.logo_id')) ?? media_url(theme('branding.logo_dark_id'));
    $siteName = setting('site_name');
@endphp

<header
    x-data="{ scrolled: false, mobileOpen: false }"
    x-init="scrolled = window.scrollY > 20"
    @scroll.window="scrolled = window.scrollY > 20"
    class="sticky top-0 z-40 overflow-visible transition-colors duration-300"
    :class="scrolled ? 'border-b border-gray-800 bg-gray-950/95 shadow-lg shadow-black/20 backdrop-blur' : 'border-b border-transparent bg-gray-950/80 backdrop-blur-sm'"
>
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 lg:px-6">
        <a href="{{ $homeUrl }}" class="flex shrink-0 items-center gap-2">
            @if (filled($logoUrl))
                <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="h-10 w-auto max-w-[180px] object-contain">
            @elseif (filled($siteName))
                <span class="text-lg font-semibold" style="color: var(--color-primary, #eab308)">{{ $siteName }}</span>
            @endif
        </a>

        <nav class="hidden items-center gap-6 text-gray-300 lg:flex [&_a:hover]:text-white" aria-label="header">
            <x-menu location="header" />
        </nav>

        <div class="hidden items-center gap-4 lg:flex">
            <x-locale-switcher />
            <x-menu location="header-cta" :cta="true" />
        </div>

        <button
            type="button"
            class="inline-flex items-center justify-center rounded-lg p-2 text-gray-300 hover:bg-gray-800 lg:hidden"
            @click="mobileOpen = ! mobileOpen"
            :aria-expanded="mobileOpen.toString()"
            aria-controls="mobile-nav"
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

    <div
        id="mobile-nav"
        x-show="mobileOpen"
        x-cloak
        x-transition
        class="border-t border-gray-800 bg-gray-950 px-4 py-4 text-gray-300 lg:hidden"
    >
        <div class="mb-4">
            <x-menu location="header" />
        </div>
        <div class="flex flex-wrap items-center gap-4">
            <x-locale-switcher />
            <x-menu location="header-cta" :cta="true" />
        </div>
    </div>
</header>
