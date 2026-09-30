@extends('theme::layouts.app')

@php
    $locale = app()->getLocale();
    $coverUrl = media_url($portfolio->cover_id);
    $categoryName = $portfolio->category?->translate($locale, false)?->name;
@endphp

@section('content')
    <x-theme::page-hero :title="$translation?->title" :subtitle="$translation?->excerpt" />

    <section class="py-12 lg:py-16">
        <div class="mx-auto max-w-7xl px-4 lg:px-6">
            @if (filled($coverUrl))
                <img src="{{ $coverUrl }}" alt="{{ $translation?->title }}" class="mb-8 w-full rounded-2xl object-cover shadow-md">
            @endif

            <dl class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @if (filled($portfolio->client_name))
                    <div>
                        <dt class="text-sm text-gray-500">{{ __('messages.client') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $portfolio->client_name }}</dd>
                    </div>
                @endif
                @if ($portfolio->project_date)
                    <div>
                        <dt class="text-sm text-gray-500">{{ __('messages.project_date') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $portfolio->project_date->translatedFormat('d M Y') }}</dd>
                    </div>
                @endif
                @if (filled($categoryName))
                    <div>
                        <dt class="text-sm text-gray-500">{{ __('messages.category') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $categoryName }}</dd>
                    </div>
                @endif
                @if (filled($portfolio->project_url))
                    <div>
                        <dt class="text-sm text-gray-500">{{ __('messages.project_url') }}</dt>
                        <dd><a href="{{ $portfolio->project_url }}" class="font-medium text-emerald-700 hover:underline" target="_blank" rel="noopener noreferrer">{{ $portfolio->project_url }}</a></dd>
                    </div>
                @endif
            </dl>

            @if (filled($translation?->content))
                <div class="prose prose-emerald max-w-none">
                    {!! $translation->content !!}
                </div>
            @endif
        </div>
    </section>

    @if ($relatedPortfolios->isNotEmpty())
        <section class="border-t border-gray-200 bg-gray-50 py-12">
            <div class="mx-auto max-w-7xl px-4 lg:px-6">
                <h2 class="mb-6 text-2xl font-bold text-gray-900">{{ __('messages.related_portfolio') }}</h2>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    @foreach ($relatedPortfolios as $related)
                        @include('theme::components.portfolio-card', ['portfolio' => $related])
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
