@props(['id' => null, 'title' => null, 'eyebrow' => null, 'link' => null, 'linkLabel' => null])

<div class="flex flex-wrap items-end justify-between gap-4">
    <div>
        @if ($eyebrow)
            <p class="section-eyebrow">{{ $eyebrow }}</p>
        @endif
        @if ($title)
            <h2 @if ($id) id="{{ $id }}" @endif class="section-title mt-1">{{ $title }}</h2>
        @endif
    </div>
    @if ($link)
        <a href="{{ $link }}" class="group inline-flex items-center gap-1.5 text-sm font-semibold text-brand-800 transition hover:text-accent-600">
            {{ $linkLabel }}
            <svg class="h-4 w-4 transition group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
            </svg>
        </a>
    @endif
</div>
