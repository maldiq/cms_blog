@php
    $locale = app()->getLocale();
    $translation = $service->translate($locale, false);
    $slug = $translation?->slug;
    $title = $translation?->title;
    $excerpt = $translation?->excerpt;
    $serviceUrl = filled($slug)
        ? route('services.show', ['locale' => $locale, 'slug' => $slug])
        : null;
@endphp

@if (filled($title) && filled($serviceUrl))
    <article class="group flex h-full flex-col rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg">
        @if (filled($service->icon))
            <span class="mb-4 text-3xl" aria-hidden="true">{{ $service->icon }}</span>
        @endif

        <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>

        @if (filled($excerpt))
            <p class="mt-2 flex-1 text-sm text-gray-600">{{ $excerpt }}</p>
        @endif

        <a
            href="{{ $serviceUrl }}"
            class="mt-4 inline-flex text-sm font-semibold transition hover:opacity-80"
            style="color: var(--color-primary, currentColor)"
        >
            {{ __('messages.more_details') }}
        </a>
    </article>
@endif
