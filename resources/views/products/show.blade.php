@extends('layouts.app')

@section('content')
    @php
        $locale = $locale ?? app()->getLocale();
        $productUrl = route('products.show', ['locale' => $locale, 'slug' => $product->slug]);
        $buyUrl = route('products.redirect', ['locale' => $locale, 'slug' => $product->slug]);
        $platformName = $product->affiliatePlatform?->name ?? __('messages.affiliate_marketplace');
        $discount = affiliate_discount_percent($product->price, $product->original_price);
        $savings = $discount !== null
            ? format_price((float) $product->original_price - (float) $product->price, $locale)
            : null;

        $currency = affiliate_currency($locale);
        $isConvertedCurrency = $currency['code'] !== config('affiliate.base_currency', 'IDR');
        $schemaPrice = affiliate_convert_price($product->price, $locale);

        $imageUrl = null;
        if ($product->image) {
            $imageUrl = $product->image->hasGeneratedConversion('large')
                ? $product->image->getUrl('large')
                : ($product->image->hasGeneratedConversion('medium') ? $product->image->getUrl('medium') : $product->image->getFullUrl());
        }

        $platformBadge = config('affiliate.badges.'.($product->affiliatePlatform?->slug ?? 'other'), [
            'label' => $platformName,
            'class' => 'bg-slate-500/95 text-white',
        ]);
        $platformBadgeClass = $platformBadge['class'] ?? 'bg-slate-500/95 text-white';

        $description = trim((string) $product->description);
        $plainDescription = trim(strip_tags($description));
        $descriptionIsHtml = $description !== ''
            && preg_match('/<[a-z][\s\S]*>/i', $description) === 1;
    @endphp

    @push('meta')
        <script type="application/ld+json">
            {!! json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'Product',
                'name' => $product->title,
                'description' => $product->excerpt ?: $plainDescription,
                'image' => $imageUrl,
                'category' => $product->affiliateCategory?->name,
                'offers' => [
                    '@type' => 'Offer',
                    'price' => $schemaPrice !== null ? number_format($schemaPrice, 2, '.', '') : null,
                    'priceCurrency' => $currency['code'],
                    'availability' => 'https://schema.org/InStock',
                    'url' => $productUrl,
                    'seller' => $product->affiliatePlatform ? [
                        '@type' => 'Organization',
                        'name' => $product->affiliatePlatform->name,
                    ] : null,
                ],
            ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_PRETTY_PRINT) !!}
        </script>
    @endpush

    <div class="border-b border-slate-200 bg-white">
        <div class="container-page py-4">
            <nav aria-label="Breadcrumb" class="flex flex-wrap items-center gap-2 text-xs text-slate-500">
                @foreach ($breadcrumbs as $index => $crumb)
                    @if ($index < count($breadcrumbs) - 1)
                        <a href="{{ $crumb['url'] }}" class="transition hover:text-brand-800">{{ $crumb['name'] }}</a>
                        <svg class="h-3.5 w-3.5 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    @else
                        <span class="line-clamp-1 font-medium text-slate-700">{{ $crumb['name'] }}</span>
                    @endif
                @endforeach
            </nav>
        </div>
    </div>

    <div class="container-page py-8 sm:py-10">
        <div class="grid gap-8 lg:grid-cols-[1.05fr_1fr] lg:gap-12">
            <div class="lg:sticky lg:top-32 lg:self-start">
                <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white p-2 shadow-sm">
                    <div class="relative aspect-square overflow-hidden rounded-[1.25rem] bg-slate-100">
                        @if ($imageUrl)
                            <img src="{{ $imageUrl }}" alt="{{ $product->title }}" class="h-full w-full object-cover" />
                        @else
                            <div class="grid h-full w-full place-items-center bg-gradient-to-br from-slate-100 to-slate-200 text-slate-400">
                                <div class="flex flex-col items-center gap-3">
                                    <svg class="h-14 w-14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true">
                                        <rect x="3" y="4" width="18" height="16" rx="2.5" />
                                        <circle cx="9" cy="10" r="1.8" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m3.5 17 4.6-4.6a1.5 1.5 0 0 1 2.1 0l3.1 3.1 2.2-2.2a1.5 1.5 0 0 1 2.1 0l2.9 2.9" />
                                    </svg>
                                    <span class="text-xs font-medium">{{ __('messages.affiliate_no_image') }}</span>
                                </div>
                            </div>
                        @endif

                        <div class="pointer-events-none absolute inset-x-4 top-4 flex items-start justify-between gap-2">
                            <span class="rounded-full px-3 py-1.5 text-[11px] font-semibold shadow-sm backdrop-blur {{ $platformBadgeClass }}">
                                {{ $platformName }}
                            </span>
                            @if ($discount)
                                <span class="rounded-full bg-rose-600 px-3 py-1.5 text-[11px] font-bold text-white shadow-sm">-{{ $discount }}%</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="flex flex-wrap items-center gap-2">
                    @if ($product->affiliateCategory)
                        <a href="{{ affiliate_category_url($locale, $product->affiliateCategory->slug) }}" class="rounded-full bg-brand-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.14em] text-brand-800 transition hover:bg-brand-100">
                            {{ $product->affiliateCategory->name }}
                        </a>
                    @endif
                    @if ($product->is_featured)
                        <span class="inline-flex items-center gap-1 rounded-full bg-accent-50 px-3 py-1 text-[11px] font-semibold text-accent-700">
                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006Z" clip-rule="evenodd" />
                            </svg>
                            {{ __('messages.affiliate_featured_badge') }}
                        </span>
                    @endif
                </div>

                <h1 class="mt-4 text-3xl font-extrabold leading-tight tracking-tight text-slate-900 sm:text-4xl">{{ $product->title }}</h1>

                <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-wrap items-baseline gap-x-3 gap-y-1">
                        <p class="text-3xl font-extrabold tracking-tight text-slate-900">
                            {{ format_price($product->price, $locale) ?? __('messages.affiliate_check_price') }}
                        </p>
                        @if ($product->original_price && (float) $product->original_price > (float) $product->price)
                            <p class="text-base text-slate-400 line-through">{{ format_price($product->original_price, $locale) }}</p>
                        @endif
                    </div>
                    @if ($savings)
                        <p class="mt-1 text-sm font-medium text-emerald-600">{{ __('messages.affiliate_save', ['amount' => $savings]) }}</p>
                    @endif
                    <p class="mt-2 text-xs text-slate-500">
                        {{ __('messages.affiliate_price_platform', ['platform' => $platformName]) }}
                    </p>
                    @if ($isConvertedCurrency && $product->price)
                        <p class="mt-1 text-xs text-slate-400">
                            {{ __('messages.affiliate_price_estimated', ['currency' => $currency['code']]) }}
                        </p>
                    @endif
                </div>

                @if ($product->excerpt)
                    <p class="mt-6 text-sm leading-7 text-slate-600">{{ $product->excerpt }}</p>
                @endif

                <div class="mt-7">
                    <a href="{{ $buyUrl }}" rel="sponsored nofollow noopener" class="btn-accent w-full justify-center py-4 text-base sm:w-auto sm:min-w-[18rem]">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-7.5M20 4l-9 9m9-9h-4.5M20 4v4.5" />
                        </svg>
                        {{ __('messages.affiliate_buy_at', ['platform' => $platformName]) }}
                    </a>

                    <p class="mt-3 max-w-xl text-xs leading-5 text-slate-500">
                        {{ __('messages.affiliate_link_disclosure') }}
                    </p>
                </div>

                <ul class="mt-6 grid gap-2.5 text-sm text-slate-600 sm:grid-cols-2">
                    <li class="flex items-center gap-2">
                        <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-emerald-50 text-emerald-600">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                        </span>
                        {{ __('messages.affiliate_trust_official_text') }}
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-emerald-50 text-emerald-600">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </span>
                        {{ __('messages.affiliate_trust_update_text') }}
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-emerald-50 text-emerald-600">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </span>
                        {{ __('messages.affiliate_trust_curated_text') }}
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-emerald-50 text-emerald-600">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                            </svg>
                        </span>
                        {{ __('messages.affiliate_trust_price_text') }}
                    </li>
                </ul>
            </div>
        </div>

        @if ($description !== '')
            <section class="mt-12 rounded-3xl border border-slate-200 bg-white p-6 sm:p-8" aria-labelledby="product-description">
                <h2 id="product-description" class="text-lg font-bold text-slate-900">{{ __('messages.affiliate_description') }}</h2>
                <div class="prose prose-emerald mt-4 max-w-none text-sm leading-7 text-slate-600">
                    @if ($descriptionIsHtml)
                        {!! $description !!}
                    @else
                        {!! nl2br(e($description)) !!}
                    @endif
                </div>
            </section>
        @endif

        @if ($relatedProducts->isNotEmpty())
            <section class="mt-14" aria-labelledby="related-products">
                <x-storefront.section-heading
                    id="related-products"
                    :eyebrow="__('messages.affiliate_more_options')"
                    :title="__('messages.affiliate_related')"
                    :link="route('products.index', ['locale' => $locale])"
                    :link-label="__('messages.affiliate_view_all')"
                />

                <div class="mt-6 grid grid-cols-2 gap-4 sm:gap-5 lg:grid-cols-4">
                    @foreach ($relatedProducts as $relatedProduct)
                        <x-product-card :product="$relatedProduct" :locale="$locale" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection
