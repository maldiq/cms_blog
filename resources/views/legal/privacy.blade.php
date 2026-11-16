@extends('layouts.app')

@section('content')
    <main class="container-page max-w-3xl py-12">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-accent-600">{{ __('messages.affiliate_disclosure_title') }}</p>
        <h1 class="mt-3 text-3xl font-extrabold tracking-tight text-brand-950 sm:text-4xl">{{ __('messages.privacy_title') }}</h1>
        <p class="mt-4 text-base leading-7 text-slate-600">{{ __('messages.privacy_intro') }}</p>

        <div class="prose prose-slate mt-10 max-w-none prose-headings:font-bold prose-headings:text-brand-950 prose-a:text-accent-600">
            <h2>{{ __('messages.privacy_tracking_title') }}</h2>
            <p>{{ __('messages.privacy_tracking_text') }}</p>

            <h2>{{ __('messages.privacy_cookies_title') }}</h2>
            <p>{{ __('messages.privacy_cookies_text') }}</p>

            <h2>{{ __('messages.privacy_data_title') }}</h2>
            <p>{{ __('messages.privacy_data_text') }}</p>

            <h2>{{ __('messages.privacy_marketplace_title') }}</h2>
            <p>{{ __('messages.privacy_marketplace_text') }}</p>

            <h2>{{ __('messages.privacy_contact_title') }}</h2>
            <p>
                {{ __('messages.privacy_contact_text') }}
                <a href="{{ route('contact', ['locale' => $locale]) }}">{{ __('messages.contact_us') }}</a>.
            </p>
        </div>
    </main>
@endsection
