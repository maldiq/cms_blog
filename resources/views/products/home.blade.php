@extends('layouts.app')

@section('content')
    @php
        $locale = $locale ?? app()->getLocale();
        $productsUrl = route('products.index', ['locale' => $locale]);
        $blogUrl = route('blog.index', ['locale' => $locale]);

        $gradients = [
            'brand' => 'from-brand-950 via-brand-900 to-brand-700',
            'accent' => 'from-accent-700 via-accent-600 to-amber-500',
            'emerald' => 'from-emerald-950 via-emerald-800 to-teal-600',
            'violet' => 'from-violet-950 via-violet-900 to-fuchsia-700',
        ];

        $cardPositions = [
            'left-0 top-0 z-20 -rotate-6',
            'left-1/2 top-14 z-10 -translate-x-1/2 rotate-2',
            'right-0 top-0 z-30 rotate-6',
        ];
    @endphp

    <h1 class="sr-only">
        {{ setting('site_name', config('app.name')) }} — {{ __('messages.affiliate_home_heading') }}
    </h1>

    <section class="container-page pt-5">
        <div class="relative overflow-hidden rounded-[1.75rem] bg-brand-950 shadow-2xl shadow-brand-950/20 sm:rounded-[2rem]" data-hero-slider>
            <div class="swiper hero-swiper" data-hero-swiper>
                <div class="swiper-wrapper">
                    @forelse ($slides as $slide)
                        @php
                            $gradient = $gradients[$slide['theme']] ?? $gradients['brand'];
                            $ctaUrl = filled($slide['link']) ? $slide['link'] : $productsUrl;
                        @endphp
                        <div class="swiper-slide">
                            <div class="relative bg-gradient-to-br {{ $gradient }}">
                                @if ($slide['image_url'])
                                    <img src="{{ $slide['image_url'] }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-30" />
                                    <div class="absolute inset-0 bg-gradient-to-r from-slate-950/85 via-slate-950/45 to-transparent" aria-hidden="true"></div>
                                @endif

                                <div class="pointer-events-none absolute -right-20 -top-24 h-72 w-72 rounded-full bg-white/10 blur-3xl" aria-hidden="true"></div>
                                <div class="pointer-events-none absolute -bottom-28 left-1/3 h-64 w-64 rounded-full bg-accent-400/20 blur-3xl" aria-hidden="true"></div>

                                <div class="relative grid items-center gap-10 p-7 pb-16 sm:p-10 sm:pb-16 md:grid-cols-[1.15fr_0.85fr] md:p-14 md:pb-16">
                                    <div>
                                        @if ($slide['badge'])
                                            <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3.5 py-1.5 text-[11px] font-semibold uppercase tracking-[0.16em] text-white ring-1 ring-inset ring-white/20">
                                                <span class="h-1.5 w-1.5 rounded-full bg-accent-400" aria-hidden="true"></span>
                                                {{ $slide['badge'] }}
                                            </span>
                                        @endif

                                        <p class="mt-5 max-w-xl text-3xl font-extrabold leading-[1.08] tracking-tight text-white sm:text-4xl lg:text-[3.25rem]">
                                            {{ $slide['title'] }}
                                        </p>

                                        @if ($slide['subtitle'])
                                            <p class="mt-4 max-w-lg text-sm leading-6 text-white/75 sm:text-base">
                                                {{ $slide['subtitle'] }}
                                            </p>
                                        @endif

                                        <div class="mt-7 flex flex-wrap items-center gap-3">
                                            <a href="{{ $ctaUrl }}" @class([
                                                'inline-flex items-center justify-center gap-2 rounded-full px-6 py-3 text-sm font-semibold shadow-lg transition',
                                                'bg-accent-500 text-white shadow-accent-500/25 hover:bg-accent-600' => $slide['theme'] !== 'accent',
                                                'bg-brand-950 text-white shadow-brand-950/25 hover:bg-brand-900' => $slide['theme'] === 'accent',
                                            ])>
                                                {{ $slide['cta_label'] }}
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                                </svg>
                                            </a>
                                            <a href="{{ $productsUrl }}" class="inline-flex items-center gap-2 rounded-full border border-white/25 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                                                {{ __('messages.affiliate_all_products') }}
                                            </a>
                                        </div>

                                        <dl class="mt-8 flex flex-wrap gap-x-8 gap-y-3">
                                            <div>
                                                <dt class="text-[11px] font-medium uppercase tracking-[0.14em] text-white/50">{{ __('messages.affiliate_stat_products') }}</dt>
                                                <dd class="mt-0.5 text-lg font-bold text-white">{{ $totalProducts }}+</dd>
                                            </div>
                                            <div>
                                                <dt class="text-[11px] font-medium uppercase tracking-[0.14em] text-white/50">{{ __('messages.affiliate_stat_categories') }}</dt>
                                                <dd class="mt-0.5 text-lg font-bold text-white">{{ $categories->count() }}</dd>
                                            </div>
                                            <div>
                                                <dt class="text-[11px] font-medium uppercase tracking-[0.14em] text-white/50">{{ __('messages.affiliate_stat_marketplaces') }}</dt>
                                                <dd class="mt-0.5 text-lg font-bold text-white">{{ $platforms->count() }}</dd>
                                            </div>
                                        </dl>
                                    </div>

                                    <div class="relative hidden h-[19rem] md:block">
                                        @forelse ($heroProducts as $index => $heroProduct)
                                            @php
                                                $heroImage = $heroProduct->image;
                                                $heroImageUrl = $heroImage
                                                    ? ($heroImage->hasGeneratedConversion('medium') ? $heroImage->getUrl('medium') : $heroImage->getUrl())
                                                    : null;
                                            @endphp
                                            <a
                                                href="{{ route('products.show', ['locale' => $locale, 'slug' => $heroProduct->slug]) }}"
                                                class="absolute {{ $cardPositions[$index] ?? 'hidden' }} w-36 rounded-2xl bg-white p-2 shadow-2xl shadow-slate-950/40 ring-1 ring-white/40 transition duration-300 hover:z-40 hover:-translate-y-1 lg:w-40"
                                            >
                                                <div class="aspect-square overflow-hidden rounded-xl bg-slate-100">
                                                    @if ($heroImageUrl)
                                                        <img src="{{ $heroImageUrl }}" alt="{{ $heroProduct->title }}" class="h-full w-full object-cover" loading="lazy" />
                                                    @else
                                                        <div class="grid h-full w-full place-items-center bg-gradient-to-br from-slate-100 to-slate-200 text-slate-400">
                                                            <x-storefront.category-icon :slug="$heroProduct->affiliateCategory?->slug" class="h-8 w-8" />
                                                        </div>
                                                    @endif
                                                </div>
                                                <p class="mt-2 line-clamp-1 px-1 text-[11px] font-semibold text-slate-800">{{ $heroProduct->title }}</p>
                                                <p class="px-1 pb-1 text-xs font-extrabold text-accent-600">
                                                    {{ format_price($heroProduct->price, $locale) ?? __('messages.affiliate_check_price') }}
                                                </p>
                                            </a>
                                        @empty
                                            <div class="absolute inset-6 grid place-items-center rounded-3xl border border-white/15 bg-white/5">
                                                <x-storefront.category-icon slug="elektronik" class="h-16 w-16 text-white/40" />
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="swiper-slide">
                            <div class="bg-gradient-to-br from-brand-950 via-brand-900 to-brand-700 p-10 md:p-14">
                                <p class="text-3xl font-extrabold text-white">{{ setting('site_name', config('app.name')) }}</p>
                                <p class="mt-3 max-w-lg text-white/70">{{ __('messages.affiliate_empty_slider') }}</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="hero-pagination swiper-pagination"></div>
            </div>

            <div class="absolute right-4 top-4 z-20 flex items-center gap-2 sm:right-8 md:bottom-8 md:top-auto">
                <button type="button" class="hero-prev grid h-10 w-10 place-items-center rounded-full border border-white/25 bg-white/10 text-white backdrop-blur transition hover:bg-white hover:text-brand-950" aria-label="{{ __('messages.prev') }}">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12l7.5-7.5M3 12h18" />
                    </svg>
                </button>
                <button type="button" class="hero-next grid h-10 w-10 place-items-center rounded-full border border-white/25 bg-white/10 text-white backdrop-blur transition hover:bg-white hover:text-brand-950" aria-label="{{ __('messages.next') }}">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12l-7.5 7.5M21 12H3" />
                    </svg>
                </button>
            </div>
        </div>
    </section>

    <section class="container-page mt-5">
        <div class="grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 sm:grid-cols-2 lg:grid-cols-4 lg:p-5">
            @foreach ([
                ['icon' => 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z', 'title' => __('messages.affiliate_trust_curated_title'), 'text' => __('messages.affiliate_trust_curated_text')],
                ['icon' => 'M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941', 'title' => __('messages.affiliate_trust_price_title'), 'text' => __('messages.affiliate_trust_price_text')],
                ['icon' => 'M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z', 'title' => __('messages.affiliate_trust_official_title'), 'text' => __('messages.affiliate_trust_official_text')],
                ['icon' => 'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z', 'title' => __('messages.affiliate_trust_update_title'), 'text' => __('messages.affiliate_trust_update_text')],
            ] as $item)
                <div class="flex items-start gap-3">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-brand-50 text-brand-800">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-900">{{ $item['title'] }}</p>
                        <p class="mt-0.5 text-xs leading-5 text-slate-500">{{ $item['text'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="container-page mt-12" aria-labelledby="kategori-heading">
        <x-storefront.section-heading
            id="kategori-heading"
            :eyebrow="__('messages.affiliate_categories')"
            :title="__('messages.affiliate_shop_by_category')"
            :link="$productsUrl"
            :link-label="__('messages.affiliate_all_categories')"
        />

        <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-8">
            @forelse ($categories->take(8) as $category)
                <a
                    href="{{ affiliate_category_url($locale, $category->slug) }}"
                    class="group flex flex-col items-center rounded-2xl border border-slate-200 bg-white px-3 py-4 text-center transition duration-300 hover:-translate-y-1 hover:border-brand-200 hover:shadow-lg hover:shadow-brand-900/5"
                >
                    <span class="grid h-12 w-12 place-items-center rounded-xl bg-brand-50 text-brand-800 transition group-hover:bg-brand-950 group-hover:text-white">
                        <x-storefront.category-icon :slug="$category->slug" class="h-6 w-6" />
                    </span>
                    <span class="mt-3 w-full truncate text-sm font-semibold text-slate-900">{{ $category->name }}</span>
                    <span class="mt-0.5 text-xs text-slate-500">{{ trans_choice('messages.affiliate_products_count', $category->products_count, ['count' => $category->products_count]) }}</span>
                </a>
            @empty
                <p class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500">
                    {{ __('messages.affiliate_no_categories') }}
                </p>
            @endforelse
        </div>
    </section>

    <section class="container-page mt-14 scroll-mt-28" id="unggulan" aria-labelledby="unggulan-heading">
        <x-storefront.section-heading
            id="unggulan-heading"
            :eyebrow="__('messages.affiliate_editor_pick')"
            :title="$featuredTitle"
            :link="$productsUrl"
            :link-label="__('messages.affiliate_view_all')"
        />

        @if ($featuredProducts->isEmpty())
            <div class="mt-6 rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center">
                <p class="text-sm text-slate-600">{{ __('messages.affiliate_empty_products') }}</p>
                <a href="{{ $productsUrl }}" class="btn-outline mt-4">{{ __('messages.affiliate_all_products') }}</a>
            </div>
        @else
            <div class="mt-6 grid grid-cols-2 gap-4 sm:gap-5 lg:grid-cols-4">
                @foreach ($featuredProducts as $product)
                    <x-product-card :product="$product" :locale="$locale" />
                @endforeach
            </div>
        @endif
    </section>

    <section class="container-page mt-14">
        <div class="grid gap-4 lg:grid-cols-3">
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-950 via-brand-900 to-brand-800 p-7 text-white sm:p-9 lg:col-span-2">
                <div class="pointer-events-none absolute -right-16 -top-16 h-56 w-56 rounded-full bg-accent-400/20 blur-3xl" aria-hidden="true"></div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-accent-300">{{ __('messages.affiliate_how_title') }}</p>
                <h2 class="mt-3 max-w-lg text-2xl font-bold leading-tight sm:text-3xl">{{ __('messages.affiliate_how_heading') }}</h2>
                <p class="mt-3 max-w-xl text-sm leading-6 text-white/70">{{ __('messages.affiliate_how_text') }}</p>

                <ul class="mt-6 grid gap-3 sm:grid-cols-3">
                    @foreach ([__('messages.affiliate_how_step_1'), __('messages.affiliate_how_step_2'), __('messages.affiliate_how_step_3')] as $index => $step)
                        <li class="rounded-2xl bg-white/10 p-4 ring-1 ring-inset ring-white/10">
                            <span class="text-xs font-bold text-accent-300">0{{ $index + 1 }}</span>
                            <p class="mt-1 text-sm font-medium leading-5 text-white">{{ $step }}</p>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-7">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.affiliate_marketplaces') }}</p>
                <ul class="mt-4 divide-y divide-slate-100">
                    @forelse ($platforms as $platform)
                        <li>
                            <a href="{{ route('products.index', ['locale' => $locale, 'platform' => $platform->slug]) }}" class="group flex items-center justify-between gap-3 py-2.5 text-sm font-medium text-slate-700 transition hover:text-brand-800">
                                <span class="flex items-center gap-2.5">
                                    <span class="h-2 w-2 rounded-full bg-brand-300 transition group-hover:bg-accent-500" aria-hidden="true"></span>
                                    {{ $platform->name }}
                                </span>
                                <svg class="h-4 w-4 text-slate-300 transition group-hover:text-accent-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                </svg>
                            </a>
                        </li>
                    @empty
                        <li class="py-3 text-sm text-slate-500">{{ __('messages.affiliate_no_platforms') }}</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </section>

    <section class="container-page mt-14 scroll-mt-28" id="produk-baru" aria-labelledby="produk-baru-heading">
        <x-storefront.section-heading
            id="produk-baru-heading"
            :eyebrow="__('messages.affiliate_new_arrivals')"
            :title="__('messages.affiliate_new_products')"
            :link="$productsUrl"
            :link-label="__('messages.affiliate_view_all')"
        />

        @if ($newProducts->isEmpty())
            <div class="mt-6 rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center">
                <p class="text-sm text-slate-600">{{ __('messages.affiliate_empty_products') }}</p>
            </div>
        @else
            <div class="mt-6 grid grid-cols-2 gap-4 sm:gap-5 lg:grid-cols-4">
                @foreach ($newProducts as $product)
                    <x-product-card :product="$product" :locale="$locale" />
                @endforeach
            </div>
        @endif
    </section>

    @if ($posts->isNotEmpty())
        <section class="container-page mt-16" aria-labelledby="blog-heading">
            <x-storefront.section-heading
                id="blog-heading"
                :eyebrow="__('messages.affiliate_blog_eyebrow')"
                :title="__('messages.affiliate_blog_title')"
                :link="$blogUrl"
                :link-label="__('messages.all_articles')"
            />

            <div class="mt-6 grid gap-5 md:grid-cols-3">
                @foreach ($posts as $post)
                    @php
                        $translation = $post->translate($locale, false);
                        $postSlug = $translation?->slug;
                        $postTitle = $translation?->title ?? $post->id;
                        $postExcerpt = $translation?->excerpt;
                        $postImage = $post->featuredImage
                            ? ($post->featuredImage->hasGeneratedConversion('medium') ? $post->featuredImage->getUrl('medium') : $post->featuredImage->getFullUrl())
                            : null;
                        $postCategory = $post->categories->first()?->translate($locale, false)?->name;
                        $postUrl = $postSlug ? route('blog.show', ['locale' => $locale, 'slug' => $postSlug]) : '#';
                    @endphp

                    <article class="group flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-brand-900/5">
                        <a href="{{ $postUrl }}" class="block aspect-[16/10] overflow-hidden bg-slate-100" tabindex="-1" aria-hidden="true">
                            @if ($postImage)
                                <img src="{{ $postImage }}" alt="" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />
                            @else
                                <span class="grid h-full w-full place-items-center bg-gradient-to-br from-brand-100 to-brand-50 text-brand-300">
                                    <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                    </svg>
                                </span>
                            @endif
                        </a>

                        <div class="flex flex-1 flex-col p-5">
                            @if ($postCategory)
                                <span class="text-[11px] font-semibold uppercase tracking-[0.14em] text-accent-600">{{ $postCategory }}</span>
                            @endif
                            <h3 class="mt-2 line-clamp-2 text-base font-semibold leading-snug text-slate-900">
                                <a href="{{ $postUrl }}" class="transition hover:text-brand-800">{{ $postTitle }}</a>
                            </h3>
                            @if ($postExcerpt)
                                <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-600">{{ $postExcerpt }}</p>
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
                @endforeach
            </div>
        </section>
    @endif

    <section class="container-page mt-16">
        <div class="relative overflow-hidden rounded-3xl bg-brand-950 px-6 py-10 sm:px-10 sm:py-12">
            <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-accent-500/20 blur-3xl" aria-hidden="true"></div>
            <div class="relative grid items-center gap-8 lg:grid-cols-2">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-accent-300">{{ __('messages.affiliate_newsletter_eyebrow') }}</p>
                    <h2 class="mt-2 text-2xl font-bold text-white sm:text-3xl">{{ __('messages.affiliate_newsletter_title') }}</h2>
                    <p class="mt-3 max-w-md text-sm leading-6 text-white/70">{{ __('messages.affiliate_newsletter_text') }}</p>
                </div>
                <div class="rounded-2xl bg-white p-5 shadow-xl" style="--color-primary: #e07c0e">
                    @livewire(\App\Domain\Newsletter\Livewire\NewsletterForm::class, [
                        'placeholder' => __('messages.affiliate_newsletter_placeholder'),
                        'buttonLabel' => __('messages.affiliate_newsletter_button'),
                    ])
                </div>
            </div>
        </div>
    </section>
@endsection
