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

    <title>@yield('title', config('app.name', 'CMS Blog'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    @stack('styles')
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">
    <header class="border-b border-gray-200 bg-white">
        <div class="mx-auto flex max-w-5xl flex-wrap items-center justify-between gap-4 px-6 py-4">
            <a href="{{ url('/' . app()->getLocale()) }}" class="text-lg font-semibold text-emerald-700">
                {{ __('messages.home') }}
            </a>
            <x-menu location="header" />
            <x-locale-switcher />
        </div>
    </header>

    @yield('content')

    <footer class="mt-auto border-t border-gray-200 bg-white">
        <div class="mx-auto max-w-5xl px-6 py-6">
            <x-menu location="footer" />
        </div>
    </footer>

    @livewireScripts
    @stack('scripts')
</body>
</html>
