@php
    $translation = $post->translate($locale, false);
    $title = $translation?->title ?? 'Post';
    $slug = $translation?->slug ?? $post->id;
    $excerpt = $translation?->excerpt;
    $imageUrl = $post->featuredImage
        ? ($post->featuredImage->hasGeneratedConversion('medium') ? $post->featuredImage->getUrl('medium') : $post->featuredImage->getFullUrl())
        : null;
    $category = $post->categories->first()?->translate($locale, false)?->name;
    $url = route('blog.show', ['locale' => $locale, 'slug' => $slug]);
@endphp

<article class="group flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-brand-900/5">
    <a href="{{ $url }}" class="block aspect-[16/10] overflow-hidden bg-slate-100" tabindex="-1" aria-hidden="true">
        @if ($imageUrl)
            <img src="{{ $imageUrl }}" alt="" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />
        @else
            <span class="grid h-full w-full place-items-center bg-gradient-to-br from-brand-100 to-brand-50 text-brand-300">
                <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                </svg>
            </span>
        @endif
    </a>

    <div class="flex flex-1 flex-col p-5">
        @if ($category)
            <span class="text-[11px] font-semibold uppercase tracking-[0.14em] text-accent-600">{{ $category }}</span>
        @endif
        <h2 class="mt-2 line-clamp-2 text-base font-semibold leading-snug text-slate-900">
            <a href="{{ $url }}" class="transition hover:text-brand-800">{{ $title }}</a>
        </h2>
        @if ($excerpt)
            <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-600">{{ $excerpt }}</p>
        @endif
        <div class="mt-auto flex items-center gap-2 pt-4 text-xs text-slate-500">
            @if ($post->published_at)
                <time datetime="{{ $post->published_at->toIso8601String() }}">{{ $post->published_at->translatedFormat('d M Y') }}</time>
            @endif
            @if ($post->reading_time)
                <span aria-hidden="true" class="text-slate-300">·</span>
                <span>{{ $post->reading_time }} {{ __('messages.affiliate_min_read') }}</span>
            @endif
        </div>
    </div>
</article>
