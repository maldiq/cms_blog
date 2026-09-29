@php
    /** @var \App\Domain\Media\Models\Media|null $media */
    $attrs = media_responsive_image($media ?? null, $lazy ?? true);
@endphp

@if (filled($attrs['src']))
    <img
        src="{{ $attrs['src'] }}"
        @if (filled($attrs['srcset'])) srcset="{{ $attrs['srcset'] }}" @endif
        sizes="{{ $attrs['sizes'] }}"
        loading="{{ $attrs['loading'] }}"
        @if (! ($lazy ?? true)) fetchpriority="high" @endif
        alt="{{ $alt ?? '' }}"
        @isset($class) class="{{ $class }}" @endisset
    >
@endif
