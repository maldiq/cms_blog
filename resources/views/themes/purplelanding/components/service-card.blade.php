@php
    $locale = app()->getLocale();
    $translation = $service->translate($locale, false);
    $title = $translation?->title;
    $excerpt = $translation?->excerpt;
@endphp

@if (filled($title))
    <article class="flex h-full flex-col rounded-2xl border border-violet-100 bg-gradient-to-b from-white to-violet-50/40 p-6 shadow-sm transition hover:border-violet-200 hover:shadow-md">
        @if (filled($service->icon))
            <span class="mb-4 text-3xl" aria-hidden="true">{{ $service->icon }}</span>
        @else
            <span class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-violet-100 text-violet-700" aria-hidden="true">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </span>
        @endif

        <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>

        @if (filled($excerpt))
            <p class="mt-2 flex-1 text-sm leading-relaxed text-gray-600">{{ $excerpt }}</p>
        @endif
    </article>
@endif
