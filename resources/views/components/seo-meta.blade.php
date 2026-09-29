<title>{{ $meta['title'] }}</title>

@if (! empty($meta['description']))
    <meta name="description" content="{{ $meta['description'] }}">
@endif

@if (! empty($meta['keywords']))
    <meta name="keywords" content="{{ $meta['keywords'] }}">
@endif

<link rel="canonical" href="{{ $meta['canonical'] }}">

@foreach ($meta['hreflang'] as $alternate)
    <link rel="alternate" hreflang="{{ $alternate['locale'] }}" href="{{ $alternate['url'] }}">
@endforeach

<meta property="og:type" content="{{ $meta['og_type'] }}">
<meta property="og:title" content="{{ $meta['og_title'] }}">
@if (! empty($meta['og_description']))
    <meta property="og:description" content="{{ $meta['og_description'] }}">
@endif
@if (! empty($meta['og_image']))
    <meta property="og:image" content="{{ $meta['og_image'] }}">
@endif
<meta property="og:url" content="{{ $meta['og_url'] }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $meta['og_title'] }}">
@if (! empty($meta['og_description']))
    <meta name="twitter:description" content="{{ $meta['og_description'] }}">
@endif
@if (! empty($meta['og_image']))
    <meta name="twitter:image" content="{{ $meta['og_image'] }}">
@endif

@foreach ($meta['json_ld'] as $schema)
    <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endforeach
