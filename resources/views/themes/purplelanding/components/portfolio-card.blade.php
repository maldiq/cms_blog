@php
    $locale = app()->getLocale();
    $translation = $portfolio->translate($locale, false);
    $slug = $translation?->slug;
    $title = $translation?->title;
    $coverUrl = media_url($portfolio->cover_id);
    $categoryName = $portfolio->category?->translate($locale, false)?->name;
    $portfolioUrl = filled($slug)
        ? route('portfolio.show', ['locale' => $locale, 'slug' => $slug])
        : null;
@endphp

@if (filled($title) && filled($portfolioUrl))
    <article class="group relative overflow-hidden rounded-xl bg-gray-100 shadow-sm">
        @if (filled($coverUrl))
            <img src="{{ $coverUrl }}" alt="{{ $title }}" class="aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy" decoding="async">
        @else
            <div class="aspect-[4/3] w-full bg-gray-200"></div>
        @endif

        <div class="absolute inset-0 flex flex-col justify-end bg-gradient-to-t from-black/75 via-black/20 to-transparent p-5 text-white">
            @if (filled($categoryName))
                <p class="text-xs uppercase tracking-wide text-white/80">{{ $categoryName }}</p>
            @endif
            <h3 class="mt-1 text-lg font-semibold">{{ $title }}</h3>
        </div>

        <a
            href="{{ $portfolioUrl }}"
            class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 transition duration-300 group-hover:opacity-100"
        >
            <span class="rounded-lg bg-white px-4 py-2 text-sm font-semibold text-gray-900">
                {{ __('messages.view_detail') }}
            </span>
        </a>
    </article>
@endif
