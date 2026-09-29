@extends('theme::layouts.app')

@php
    $coverUrl = media_url($service->cover_id);
    $consultUrl = route('contact', ['locale' => $locale]);
@endphp

@section('content')
    <x-theme::page-hero :title="$translation?->title" :subtitle="$translation?->excerpt" />

    <section class="py-12 lg:py-16">
        <div class="mx-auto grid max-w-7xl grid-cols-1 gap-10 px-4 lg:grid-cols-3 lg:px-6">
            <div class="lg:col-span-2 space-y-6">
                @if (filled($coverUrl))
                    <img src="{{ $coverUrl }}" alt="{{ $translation?->title }}" class="w-full rounded-2xl object-cover shadow-md">
                @endif

                @if (filled($translation?->content))
                    <div class="prose prose-emerald max-w-none">
                        {!! $translation->content !!}
                    </div>
                @endif
            </div>

            <aside class="space-y-4 rounded-xl border border-gray-200 bg-gray-50 p-6">
                @if (filled($service->price_from))
                    <p class="text-sm text-gray-500">{{ __('messages.price_from') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format((float) $service->price_from, 0, ',', '.') }}</p>
                @endif

                <a
                    href="{{ $consultUrl }}"
                    class="inline-flex w-full justify-center rounded-lg px-4 py-3 text-sm font-semibold text-white transition hover:opacity-90"
                    style="background-color: var(--color-primary, currentColor)"
                >
                    {{ __('messages.consultation_cta') }}
                </a>
            </aside>
        </div>
    </section>

    @if ($relatedServices->isNotEmpty())
        <section class="border-t border-gray-200 bg-gray-50 py-12">
            <div class="mx-auto max-w-7xl px-4 lg:px-6">
                <h2 class="mb-6 text-2xl font-bold text-gray-900">{{ __('messages.related_services') }}</h2>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    @foreach ($relatedServices as $related)
                        @include('theme::components.service-card', ['service' => $related])
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
