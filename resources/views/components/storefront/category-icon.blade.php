@props(['slug' => null, 'class' => 'h-5 w-5'])

@php
    $key = strtolower((string) $slug);

    $icon = match (true) {
        str_contains($key, 'elektronik'), str_contains($key, 'gadget'), str_contains($key, 'komputer') => 'device',
        str_contains($key, 'rumah') => 'home',
        str_contains($key, 'dapur') => 'kitchen',
        str_contains($key, 'kecantikan'), str_contains($key, 'perawatan') => 'sparkles',
        str_contains($key, 'buku') => 'book',
        str_contains($key, 'hosting'), str_contains($key, 'vps'), str_contains($key, 'software') => 'server',
        str_contains($key, 'fashion'), str_contains($key, 'aksesoris') => 'bag',
        str_contains($key, 'olahraga'), str_contains($key, 'hobi'), str_contains($key, 'hobby') => 'trophy',
        default => 'tag',
    };
@endphp

<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
    @switch($icon)
        @case('device')
            <rect x="6" y="3" width="12" height="18" rx="2.5" />
            <path stroke-linecap="round" d="M11 18h2" />
            @break

        @case('home')
            <path stroke-linecap="round" stroke-linejoin="round" d="m3 10.5 9-7.5 9 7.5V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-8.5Z" />
            <path stroke-linecap="round" d="M9.5 21v-6h5v6" />
            @break

        @case('kitchen')
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 10h16v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-8Z" />
            <path stroke-linecap="round" d="M8 10V7a4 4 0 0 1 8 0v3M12 6v4" />
            @break

        @case('sparkles')
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3.5 13.7 9l5.5 1.7-5.5 1.7L12 18l-1.7-5.6L4.8 10.7 10.3 9 12 3.5Z" />
            <path stroke-linecap="round" d="M18.5 16.5 19.2 19l2.5.7-2.5.7-.7 2.5-.7-2.5-2.5-.7 2.5-.7.7-2.5Z" />
            @break

        @case('book')
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v15.5H6.5A2.5 2.5 0 0 0 4 21V5.5Z" />
            <path stroke-linecap="round" d="M8 7h8M8 11h6" />
            @break

        @case('server')
            <rect x="3.5" y="4" width="17" height="6" rx="2" />
            <rect x="3.5" y="14" width="17" height="6" rx="2" />
            <path stroke-linecap="round" d="M7 7h.01M7 17h.01M16.5 7H17M16.5 17H17" />
            @break

        @case('bag')
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 8h12l1 12.25A.75.75 0 0 1 18.25 21H5.75a.75.75 0 0 1-.75-.75L6 8Z" />
            <path stroke-linecap="round" d="M9 8V6a3 3 0 0 1 6 0v2" />
            @break

        @case('trophy')
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 4h8v4a4 4 0 0 1-8 0V4Z" />
            <path stroke-linecap="round" d="M8 5H5v1.5A3.5 3.5 0 0 0 8.5 10M16 5h3v1.5a3.5 3.5 0 0 1-3.5 3.5M12 12v5M9 20h6" />
            @break

        @default
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.5 11.2V5a1.5 1.5 0 0 1 1.5-1.5h6.2a1.5 1.5 0 0 1 1.06.44l8.09 8.09a2.25 2.25 0 0 1 0 3.18l-4.24 4.24a2.25 2.25 0 0 1-3.18 0l-8.09-8.09a1.5 1.5 0 0 1-.44-1.06Z" />
            <path stroke-linecap="round" d="M7.5 7.5h.01" />
    @endswitch
</svg>
