@extends('theme::layouts.app')

@section('content')
    <x-theme::page-hero :title="theme_locale('faq.title')" :subtitle="theme_locale('faq.subtitle')" />
    <x-theme::faq-section />

    <section class="pb-16 text-center">
        <a
            href="{{ route('contact', ['locale' => app()->getLocale()]) }}"
            class="inline-flex rounded-lg px-6 py-3 text-sm font-semibold text-white transition hover:opacity-90"
            style="background-color: var(--color-primary, currentColor)"
        >
            {{ __('messages.contact_us') }}
        </a>
    </section>
@endsection
