<!DOCTYPE html>
@php
    $currentLanguage = \App\Domain\Language\Models\Language::query()
        ->where('code', app()->getLocale())
        ->first();
    $textDirection = $currentLanguage?->direction ?? 'ltr';
    $fontHeading = theme('branding.font_heading');
    $fontBody = theme('branding.font_body');
    $googleFonts = collect([$fontHeading, $fontBody])
        ->filter(fn ($font) => filled($font))
        ->unique()
        ->values();
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $textDirection }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <x-seo-meta
        :model="$seo ?? null"
        :locale="$locale ?? null"
        :breadcrumbs="$breadcrumbs ?? []"
        :context="$seoContext ?? []"
        :website-schema="$websiteSchema ?? false"
    />

    @stack('meta')

    @if ($googleFonts->isNotEmpty())
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?{{ $googleFonts->map(fn (string $font): string => 'family=' . str_replace(' ', '+', $font) . ':wght@400;500;600;700')->implode('&') }}&display=swap"
            rel="stylesheet"
        >
    @endif

    @if (filled(theme('branding.primary_color')) || filled(theme('branding.secondary_color')) || filled(theme('branding.accent_color')) || filled($fontHeading) || filled($fontBody))
        <style>
            :root {
                @if (filled(theme('branding.primary_color')))
                    --color-primary: {{ theme('branding.primary_color') }};
                @endif
                @if (filled(theme('branding.secondary_color')))
                    --color-secondary: {{ theme('branding.secondary_color') }};
                @endif
                @if (filled(theme('branding.accent_color')))
                    --color-accent: {{ theme('branding.accent_color') }};
                @endif
            }
            @if (filled($fontBody))
                body { font-family: '{{ $fontBody }}', ui-sans-serif, system-ui, sans-serif; }
            @endif
            @if (filled($fontHeading))
                h1, h2, h3, h4, h5, h6 { font-family: '{{ $fontHeading }}', ui-serif, Georgia, serif; }
            @endif
        </style>
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    @include('theme::components.purple-skin')

    @stack('styles')
</head>
<body class="purplelanding-theme min-h-screen text-gray-900 antialiased">
    <x-theme::header />

    @yield('content')

    <x-theme::footer />

    @include('theme::components.back-to-top')

    @livewireScripts
    @stack('scripts')
</body>
</html>
