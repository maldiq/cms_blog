@php
    $platform = $product->affiliatePlatform;
    $platformName = $platform?->name ?? __('messages.affiliate_marketplace');

    $badges = config('affiliate.badges', []);
    $badgeClass = $badges[$platform?->slug ?? 'other']['class'] ?? 'bg-slate-500/95 text-white';

    $imageMedia = $product->image;
    $imageUrl = $imageMedia
        ? ($imageMedia->hasGeneratedConversion('medium') ? $imageMedia->getUrl('medium') : $imageMedia->getUrl())
        : null;

    $discount = affiliate_discount_percent($product->price, $product->original_price);
    $priceLabel = format_price($product->price, $locale) ?? __('messages.affiliate_check_price');
    $originalPrice = format_price($product->original_price, $locale);
    $productUrl = route('products.show', ['locale' => $locale, 'slug' => $product->slug]);
    $buyUrl = route('products.redirect', ['locale' => $locale, 'slug' => $product->slug]);
@endphp

<article class="group relative flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white transition duration-300 hover:-translate-y-1 hover:border-brand-200 hover:shadow-xl hover:shadow-brand-900/5">
    <div class="relative aspect-square overflow-hidden bg-slate-100">
        <a href="{{ $productUrl }}" class="block h-full w-full" aria-label="{{ $product->title }}">
            @if ($imageUrl)
                <img
                    src="{{ $imageUrl }}"
                    alt="{{ $product->title }}"
                    loading="lazy"
                    class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                />
            @else
                <span class="flex h-full w-full flex-col items-center justify-center gap-2 bg-gradient-to-br from-slate-100 to-slate-200 text-slate-400">
                    <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
                        <rect x="3" y="4" width="18" height="16" rx="2.5" />
                        <circle cx="9" cy="10" r="1.8" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="m3.5 17 4.6-4.6a1.5 1.5 0 0 1 2.1 0l3.1 3.1 2.2-2.2a1.5 1.5 0 0 1 2.1 0l2.9 2.9" />
                    </svg>
                    <span class="text-[11px] font-medium">{{ __('messages.affiliate_no_image') }}</span>
                </span>
            @endif
        </a>

        <div class="pointer-events-none absolute inset-x-3 top-3 flex items-start justify-between gap-2">
            <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold shadow-sm backdrop-blur {{ $badgeClass }}">
                {{ $platformName }}
            </span>
            @if ($discount)
                <span class="rounded-full bg-rose-600 px-2 py-1 text-[11px] font-bold text-white shadow-sm">-{{ $discount }}%</span>
            @elseif ($product->is_featured)
                <span class="rounded-full bg-accent-500 px-2 py-1 text-[11px] font-bold text-white shadow-sm">{{ __('messages.affiliate_featured_badge') }}</span>
            @endif
        </div>
    </div>

    <div class="flex flex-1 flex-col p-4">
        @if ($product->affiliateCategory)
            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-accent-600">{{ $product->affiliateCategory->name }}</p>
        @endif

        <h3 class="mt-1.5 line-clamp-2 min-h-[2.5rem] text-sm font-semibold leading-snug text-slate-900">
            <a href="{{ $productUrl }}" class="transition hover:text-brand-800">{{ $product->title }}</a>
        </h3>

        <div class="mt-auto pt-4">
            <div class="flex flex-wrap items-baseline gap-x-2 gap-y-1">
                <p class="text-base font-extrabold text-slate-900">{{ $priceLabel }}</p>
                @if ($originalPrice)
                    <p class="text-xs text-slate-400 line-through">{{ $originalPrice }}</p>
                @endif
            </div>

            <div class="mt-3 flex items-center gap-2">
                <a
                    href="{{ $buyUrl }}"
                    rel="sponsored nofollow noopener"
                    class="inline-flex flex-1 items-center justify-center gap-1.5 whitespace-nowrap rounded-full bg-accent-500 px-3 py-2 text-xs font-semibold text-white transition hover:bg-accent-600"
                >
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-7.5M20 4l-9 9m9-9h-4.5M20 4v4.5" />
                    </svg>
                    {{ __('messages.affiliate_buy_now') }}
                </a>
                <a
                    href="{{ $productUrl }}"
                    class="hidden items-center justify-center whitespace-nowrap rounded-full border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-brand-300 hover:text-brand-800 sm:inline-flex"
                >
                    {{ __('messages.affiliate_detail') }}
                </a>
            </div>
        </div>
    </div>
</article>
