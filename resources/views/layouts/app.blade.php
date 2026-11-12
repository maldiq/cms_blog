<!DOCTYPE html>
@php
    $currentLanguage = \App\Domain\Language\Models\Language::query()
        ->where('code', app()->getLocale())
        ->first();
    $textDirection = $currentLanguage?->direction ?? 'ltr';
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $textDirection }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1b283d">

    <x-seo-meta
        :model="$seo ?? null"
        :locale="$locale ?? null"
        :breadcrumbs="$breadcrumbs ?? []"
        :context="$seoContext ?? []"
        :website-schema="$websiteSchema ?? false"
    />

    @stack('meta')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    @stack('styles')
</head>
<body class="flex min-h-screen flex-col bg-slate-50 text-slate-800 antialiased">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-full focus:bg-brand-950 focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-white">
        {{ __('messages.affiliate_skip_to_content') }}
    </a>

    <x-storefront.header />

    <div id="main-content" class="w-full min-w-0 flex-1">
        @yield('content')
    </div>

    <x-storefront.footer />

    <div x-data="{ visible: false }" x-on:scroll.window.passive="visible = window.scrollY > 600" x-cloak>
        <button
            type="button"
            x-show="visible"
            x-transition
            x-on:click="window.scrollTo({ top: 0, behavior: 'smooth' })"
            class="fixed bottom-5 right-5 z-40 grid h-11 w-11 place-items-center rounded-full bg-brand-950 text-white shadow-lg shadow-brand-950/25 transition hover:bg-accent-500"
            aria-label="{{ __('messages.back_to_top') }}"
        >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19.5v-15m0 0-6 6m6-6 6 6" />
            </svg>
        </button>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
