@extends('layouts.app')

@section('content')
    @php
        $locale = $locale ?? app()->getLocale();
        $activePlatform = is_string(request('platform')) && filled(request('platform')) ? request('platform') : null;
        $activeSearch = is_string(request('q')) && filled(request('q')) ? request('q') : null;

        $categoryRoute = $locale === 'id' ? 'products.category' : 'products.category.intl';

        $catalogAction = $selectedCategory
            ? route($categoryRoute, ['locale' => $locale, 'slug' => $selectedCategory->slug])
            : route('products.index', ['locale' => $locale]);

        $filterQuery = collect(request()->query())
            ->except(['page', 'sort', 'category', 'featured'])
            ->filter(fn ($value): bool => is_string($value) && filled($value))
            ->all();

        $categoryLink = fn ($slug): string => $slug === null
            ? route('products.index', ['locale' => $locale, ...$filterQuery])
            : route($categoryRoute, ['locale' => $locale, 'slug' => $slug, ...$filterQuery]);

        $platformLink = fn (string $slug): string => route($selectedCategory ? $categoryRoute : 'products.index', [
            'locale' => $locale,
            ...($selectedCategory ? ['slug' => $selectedCategory->slug] : []),
            ...$filterQuery,
            'platform' => $activePlatform === $slug ? null : $slug,
        ]);

        $pageTitle = $selectedCategory?->name ?? __('messages.affiliate_all_products');
        $pageDescription = $selectedCategory
            ? __('messages.affiliate_category_intro', ['category' => $selectedCategory->name])
            : __('messages.affiliate_catalog_intro');
    @endphp

    <section class="border-b border-slate-200 bg-white">
        <div class="container-page py-7 sm:py-9">
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-xs text-slate-500">
                <a href="{{ route('home', ['locale' => $locale]) }}" class="transition hover:text-brand-800">{{ __('messages.home') }}</a>
                <svg class="h-3.5 w-3.5 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
                @if ($selectedCategory)
                    <a href="{{ route('products.index', ['locale' => $locale]) }}" class="transition hover:text-brand-800">{{ __('messages.affiliate_all_products') }}</a>
                    <svg class="h-3.5 w-3.5 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                    <span class="font-medium text-slate-700">{{ $selectedCategory->name }}</span>
                @else
                    <span class="font-medium text-slate-700">{{ __('messages.affiliate_all_products') }}</span>
                @endif
            </nav>

            <div class="mt-5 flex flex-wrap items-end justify-between gap-5">
                <div>
                    <p class="section-eyebrow">{{ __('messages.affiliate_catalog') }}</p>
                    <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ $pageTitle }}</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">{{ $pageDescription }}</p>
                </div>

                <form method="GET" action="{{ $catalogAction }}" role="search" class="relative hidden w-full max-w-sm md:block">
                    <label for="catalog-search" class="sr-only">{{ __('messages.affiliate_search_label') }}</label>
                    <svg class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z" />
                    </svg>
                    @if ($activePlatform)
                        <input type="hidden" name="platform" value="{{ $activePlatform }}" />
                    @endif
                    @if ($sort !== 'latest')
                        <input type="hidden" name="sort" value="{{ $sort }}" />
                    @endif
                    <input
                        id="catalog-search"
                        type="search"
                        name="q"
                        value="{{ $activeSearch }}"
                        placeholder="{{ __('messages.affiliate_search_placeholder') }}"
                        class="h-11 w-full rounded-full border border-slate-200 bg-slate-50 pl-11 pr-4 text-sm outline-none transition placeholder:text-slate-400 focus:border-brand-300 focus:bg-white focus:ring-4 focus:ring-brand-100"
                    />
                </form>
            </div>
        </div>
    </section>

    <div class="container-page py-8">
        <div class="lg:hidden">
            <div class="-mx-4 flex gap-2 overflow-x-auto px-4 pb-1 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                <a href="{{ $categoryLink(null) }}" @class([
                    'shrink-0 rounded-full border px-3.5 py-1.5 text-xs font-semibold transition',
                    'border-brand-900 bg-brand-950 text-white' => $selectedCategory === null,
                    'border-slate-200 bg-white text-slate-600' => $selectedCategory !== null,
                ])>{{ __('messages.affiliate_all_products') }}</a>
                @foreach ($categories as $category)
                    <a href="{{ $categoryLink($category->slug) }}" @class([
                        'shrink-0 rounded-full border px-3.5 py-1.5 text-xs font-semibold transition',
                        'border-brand-900 bg-brand-950 text-white' => $selectedCategory?->slug === $category->slug,
                        'border-slate-200 bg-white text-slate-600' => $selectedCategory?->slug !== $category->slug,
                    ])>{{ $category->name }}</a>
                @endforeach
            </div>

            <form method="GET" action="{{ $catalogAction }}" class="mt-3 flex items-center gap-2">
                @foreach ($filterQuery as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}" />
                @endforeach
                <label for="mobile-sort" class="text-xs font-medium text-slate-500">{{ __('messages.affiliate_sort_by') }}</label>
                <select id="mobile-sort" name="sort" onchange="this.form.submit()" class="flex-1 rounded-full border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-700">
                    @foreach ($sortOptions as $value => $label)
                        <option value="{{ $value }}" @selected($sort === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="grid gap-8 lg:grid-cols-[15.5rem_1fr]">
            <aside class="hidden lg:block">
                <div class="sticky top-32 space-y-4">
                    <section class="rounded-2xl border border-slate-200 bg-white p-5" aria-labelledby="filter-category">
                        <h2 id="filter-category" class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">{{ __('messages.affiliate_categories') }}</h2>
                        <ul class="mt-3 space-y-1 text-sm">
                            <li>
                                <a href="{{ $categoryLink(null) }}" @class([
                                    'flex items-center justify-between rounded-lg px-2.5 py-2 transition',
                                    'bg-brand-50 font-semibold text-brand-900' => $selectedCategory === null,
                                    'text-slate-600 hover:bg-slate-50 hover:text-brand-800' => $selectedCategory !== null,
                                ])>
                                    <span>{{ __('messages.affiliate_all_products') }}</span>
                                </a>
                            </li>
                            @foreach ($categories as $category)
                                <li>
                                    <a href="{{ $categoryLink($category->slug) }}" @class([
                                        'flex items-center justify-between gap-2 rounded-lg px-2.5 py-2 transition',
                                        'bg-brand-50 font-semibold text-brand-900' => $selectedCategory?->slug === $category->slug,
                                        'text-slate-600 hover:bg-slate-50 hover:text-brand-800' => $selectedCategory?->slug !== $category->slug,
                                    ])>
                                        <span class="truncate">{{ $category->name }}</span>
                                        <span class="shrink-0 text-xs text-slate-400">{{ $category->products_count }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </section>

                    <section class="rounded-2xl border border-slate-200 bg-white p-5" aria-labelledby="filter-platform">
                        <h2 id="filter-platform" class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">{{ __('messages.affiliate_marketplaces') }}</h2>
                        <ul class="mt-3 space-y-1 text-sm">
                            @foreach ($platforms as $platform)
                                <li>
                                    <a href="{{ $platformLink($platform->slug) }}" @class([
                                        'flex items-center justify-between gap-2 rounded-lg px-2.5 py-2 transition',
                                        'bg-brand-50 font-semibold text-brand-900' => $activePlatform === $platform->slug,
                                        'text-slate-600 hover:bg-slate-50 hover:text-brand-800' => $activePlatform !== $platform->slug,
                                    ])>
                                        <span class="truncate">{{ $platform->name }}</span>
                                        <span class="shrink-0 text-xs text-slate-400">{{ $platform->products_count }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </section>

                    <form method="GET" action="{{ $catalogAction }}" class="rounded-2xl border border-slate-200 bg-white p-5">
                        @foreach ($filterQuery as $key => $value)
                            @if (! in_array($key, ['min_price', 'max_price'], true))
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}" />
                            @endif
                        @endforeach
                        <label class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">{{ __('messages.affiliate_price_range') }}</label>
                        <div class="mt-3 flex items-center gap-2">
                            <input type="number" name="min_price" min="0" value="{{ request('min_price') }}" placeholder="Min" class="w-full rounded-lg border border-slate-200 px-2.5 py-2 text-xs outline-none focus:border-brand-300" />
                            <span class="text-slate-300">—</span>
                            <input type="number" name="max_price" min="0" value="{{ request('max_price') }}" placeholder="Max" class="w-full rounded-lg border border-slate-200 px-2.5 py-2 text-xs outline-none focus:border-brand-300" />
                        </div>
                        <button type="submit" class="mt-3 w-full rounded-full bg-slate-900 px-3 py-2 text-xs font-semibold text-white transition hover:bg-brand-900">
                            {{ __('messages.affiliate_apply_filter') }}
                        </button>
                    </form>

                    @if ($filterQuery !== [] || $activeSearch)
                        <a href="{{ $catalogAction }}" class="block text-center text-xs font-semibold text-slate-500 transition hover:text-brand-800">
                            {{ __('messages.affiliate_reset_filter') }}
                        </a>
                    @endif
                </div>
            </aside>

            <section aria-labelledby="catalog-results">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p id="catalog-results" class="text-sm text-slate-600">
                        <span class="font-semibold text-slate-900">{{ $products->total() }}</span>
                        {{ __('messages.affiliate_products_found') }}
                        @if ($activeSearch)
                            <span class="text-slate-400">·</span> "{{ $activeSearch }}"
                        @endif
                    </p>

                    <form method="GET" action="{{ $catalogAction }}" class="hidden items-center gap-2 lg:flex">
                        @foreach ($filterQuery as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}" />
                        @endforeach
                        <label for="desktop-sort" class="text-xs font-medium text-slate-500">{{ __('messages.affiliate_sort_by') }}</label>
                        <select id="desktop-sort" name="sort" onchange="this.form.submit()" class="rounded-full border border-slate-200 bg-white px-3.5 py-2 text-xs font-medium text-slate-700">
                            @foreach ($sortOptions as $value => $label)
                                <option value="{{ $value }}" @selected($sort === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                @if ($products->isEmpty())
                    <div class="mt-6 rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center">
                        <span class="mx-auto grid h-14 w-14 place-items-center rounded-full bg-slate-100 text-slate-400">
                            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z" />
                            </svg>
                        </span>
                        <p class="mt-4 text-base font-semibold text-slate-900">{{ __('messages.affiliate_no_results') }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ __('messages.affiliate_no_results_text') }}</p>
                        <a href="{{ $catalogAction }}" class="btn-outline mt-5">{{ __('messages.affiliate_reset_filter') }}</a>
                    </div>
                @else
                    <div class="mt-5 grid grid-cols-2 gap-4 sm:gap-5 xl:grid-cols-3">
                        @foreach ($products as $product)
                            <x-product-card :product="$product" :locale="$locale" />
                        @endforeach
                    </div>

                    <div class="mt-10">
                        {{ $products->links() }}
                    </div>
                @endif
            </section>
        </div>
    </div>
@endsection
