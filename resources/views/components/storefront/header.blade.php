@php
    use App\Domain\Language\Models\Language;
    use App\Domain\Language\Support\LocaleSwitchUrl;

    $locale = app()->getLocale();
    $homeUrl = route('home', ['locale' => $locale]);
    $productsUrl = route('products.index', ['locale' => $locale]);
    $blogUrl = route('blog.index', ['locale' => $locale]);
    $contactUrl = route('contact', ['locale' => $locale]);
@endphp

<div x-data="{ menuOpen: false }" x-effect="document.body.classList.toggle('overflow-hidden', menuOpen)">
    <div class="bg-brand-950 text-brand-200">
        <div class="container-page flex h-9 items-center justify-between gap-4 text-[11px] sm:text-xs">
            <p class="flex min-w-0 items-center gap-2">
                <span class="hidden h-1.5 w-1.5 shrink-0 rounded-full bg-accent-400 sm:block" aria-hidden="true"></span>
                <span class="truncate">{{ __('messages.affiliate_topbar') }}</span>
            </p>
            <div class="flex shrink-0 items-center gap-3 sm:gap-4">
                <a href="{{ $blogUrl }}" class="transition hover:text-white">{{ __('messages.blog') }}</a>
                <a href="{{ $contactUrl }}" class="hidden transition hover:text-white sm:inline">{{ __('messages.contact_us') }}</a>
                <span class="hidden h-3 w-px bg-white/20 sm:block" aria-hidden="true"></span>
                <div class="flex items-center gap-2" aria-label="{{ __('messages.language') }}">
                    @foreach (Language::getActive() as $language)
                        <a
                            href="{{ LocaleSwitchUrl::forLocale($language->code) }}"
                            @class([
                                'rounded px-1 py-0.5 font-semibold uppercase tracking-wide transition',
                                'bg-white/15 text-white' => $language->code === $locale,
                                'text-brand-300 hover:text-white' => $language->code !== $locale,
                            ])
                            @if ($language->code === $locale) aria-current="true" @endif
                        >
                            {{ $language->code }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 shadow-sm backdrop-blur">
        <div class="container-page flex h-16 items-center gap-3 sm:h-[4.5rem] lg:gap-6">
            <a href="{{ $homeUrl }}" class="flex shrink-0 items-center gap-2.5" aria-label="{{ $siteName }}">
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="h-10 w-auto max-w-[10.5rem] object-contain" />
                @else
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br from-brand-700 to-brand-950 text-white shadow-md shadow-brand-900/20">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12A1.125 1.125 0 0 1 19.751 22H4.249a1.125 1.125 0 0 1-1.118-1.493l1.263-12A1.125 1.125 0 0 1 5.512 7.5h12.976a1.125 1.125 0 0 1 1.118.993Z" />
                        </svg>
                    </span>
                @endif
                <span class="min-w-0">
                    <span class="block truncate text-base font-extrabold leading-tight tracking-tight text-brand-950">{{ $siteName }}</span>
                    <span class="hidden text-[11px] font-medium text-slate-500 sm:block">{{ __('messages.affiliate_tagline') }}</span>
                </span>
            </a>

            <form method="GET" action="{{ $productsUrl }}" role="search" class="relative hidden flex-1 md:block">
                <label for="header-search" class="sr-only">{{ __('messages.affiliate_search_label') }}</label>
                <svg class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z" />
                </svg>
                <input
                    id="header-search"
                    type="search"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="{{ __('messages.affiliate_search_placeholder') }}"
                    class="h-11 w-full rounded-full border border-slate-200 bg-slate-50 pl-11 pr-28 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-brand-300 focus:bg-white focus:ring-4 focus:ring-brand-100"
                />
                <button type="submit" class="absolute right-1 top-1 inline-flex h-9 items-center rounded-full bg-accent-500 px-4 text-xs font-semibold text-white transition hover:bg-accent-600">
                    {{ __('messages.affiliate_search_button') }}
                </button>
            </form>

            <div class="ml-auto flex items-center gap-2">
                <a href="{{ route('home', ['locale' => $locale]) }}#unggulan" class="hidden items-center gap-1.5 rounded-full px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-brand-900 xl:inline-flex">
                    <svg class="h-4 w-4 text-accent-500" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006Z" clip-rule="evenodd" />
                    </svg>
                    {{ __('messages.affiliate_featured') }}
                </a>
                <a href="{{ $productsUrl }}" class="hidden rounded-full bg-brand-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-800 lg:inline-flex">
                    {{ __('messages.affiliate_all_products') }}
                </a>
                <button
                    type="button"
                    class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-slate-200 text-slate-700 transition hover:border-brand-300 hover:text-brand-800 lg:hidden"
                    aria-label="{{ __('messages.toggle_menu') }}"
                    aria-controls="storefront-mobile-menu"
                    x-on:click="menuOpen = true"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="container-page pb-3 md:hidden">
            <form method="GET" action="{{ $productsUrl }}" role="search" class="relative">
                <label for="mobile-search" class="sr-only">{{ __('messages.affiliate_search_label') }}</label>
                <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z" />
                </svg>
                <input
                    id="mobile-search"
                    type="search"
                    name="q"
                    placeholder="{{ __('messages.affiliate_search_placeholder') }}"
                    class="h-10 w-full rounded-full border border-slate-200 bg-slate-50 pl-10 pr-4 text-sm outline-none focus:border-brand-300 focus:bg-white focus:ring-4 focus:ring-brand-100"
                />
            </form>
        </div>

        <nav class="hidden border-t border-slate-100 lg:block" aria-label="{{ __('messages.affiliate_main_nav') }}">
            <div class="container-page flex h-11 items-center gap-1 text-sm font-medium text-slate-600">
                <a href="{{ $homeUrl }}" @class(['rounded-full px-3 py-1.5 transition hover:bg-slate-100 hover:text-brand-900', 'bg-brand-50 text-brand-900' => request()->routeIs('home')])>
                    {{ __('messages.home') }}
                </a>
                <a href="{{ $productsUrl }}" @class(['rounded-full px-3 py-1.5 transition hover:bg-slate-100 hover:text-brand-900', 'bg-brand-50 text-brand-900' => request()->routeIs('products.*') && ! request()->routeIs('products.category', 'products.category.intl')])>
                    {{ __('messages.affiliate_all_products') }}
                </a>

                <div class="relative" x-data="{ open: false }" x-on:mouseenter="open = true" x-on:mouseleave="open = false" x-on:keydown.escape.window="open = false">
                    <button
                        type="button"
                        class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 transition hover:bg-slate-100 hover:text-brand-900"
                        x-bind:aria-expanded="open.toString()"
                        aria-haspopup="true"
                        x-on:click="open = ! open"
                    >
                        {{ __('messages.affiliate_categories') }}
                        <svg class="h-3.5 w-3.5 text-slate-400 transition" x-bind:class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.25a.75.75 0 0 1-1.06 0L5.21 8.29a.75.75 0 0 1 .02-1.08Z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div
                        x-cloak
                        x-show="open"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-1"
                        class="absolute left-0 top-full z-50 w-[42rem] max-w-[calc(100vw-2rem)] pt-2"
                    >
                        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white p-3 shadow-2xl shadow-slate-900/10">
                            <div class="grid grid-cols-2 gap-1">
                                @forelse ($categories as $category)
                                    <a
                                        href="{{ affiliate_category_url($locale, $category->slug) }}"
                                        class="group flex items-center gap-3 rounded-xl px-3 py-2.5 transition hover:bg-brand-50"
                                    >
                                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-slate-100 text-brand-800 transition group-hover:bg-white group-hover:text-brand-900">
                                            <x-storefront.category-icon :slug="$category->slug" class="h-4 w-4" />
                                        </span>
                                        <span class="min-w-0">
                                            <span class="block truncate text-sm font-semibold text-slate-800">{{ $category->name }}</span>
                                            <span class="block text-xs text-slate-500">{{ trans_choice('messages.affiliate_products_count', $category->products_count, ['count' => $category->products_count]) }}</span>
                                        </span>
                                    </a>
                                @empty
                                    <p class="col-span-2 px-3 py-4 text-sm text-slate-500">{{ __('messages.affiliate_no_categories') }}</p>
                                @endforelse
                            </div>
                            <div class="mt-2 border-t border-slate-100 pt-2">
                                <a href="{{ $productsUrl }}" class="flex items-center justify-between rounded-xl bg-brand-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-900">
                                    {{ __('messages.affiliate_all_categories') }}
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="{{ $homeUrl }}#unggulan" class="rounded-full px-3 py-1.5 transition hover:bg-slate-100 hover:text-brand-900">
                    {{ __('messages.affiliate_featured') }}
                </a>
                <a href="{{ $homeUrl }}#produk-baru" class="rounded-full px-3 py-1.5 transition hover:bg-slate-100 hover:text-brand-900">
                    {{ __('messages.affiliate_new_products') }}
                </a>
                <a href="{{ $blogUrl }}" @class(['rounded-full px-3 py-1.5 transition hover:bg-slate-100 hover:text-brand-900', 'bg-brand-50 text-brand-900' => request()->routeIs('blog.*')])>
                    {{ __('messages.blog') }}
                </a>
                <a href="{{ $contactUrl }}" class="rounded-full px-3 py-1.5 transition hover:bg-slate-100 hover:text-brand-900">
                    {{ __('messages.contact_us') }}
                </a>

                <span class="ml-auto hidden items-center gap-2 text-xs font-medium text-slate-500 xl:flex">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500" aria-hidden="true"></span>
                    {{ __('messages.affiliate_curated_count', ['count' => $productCount, 'platforms' => $platformCount]) }}
                </span>
            </div>
        </nav>
    </header>

    <div
        x-cloak
        x-show="menuOpen"
        class="fixed inset-0 z-50 lg:hidden"
        role="dialog"
        aria-modal="true"
        aria-label="{{ __('messages.toggle_menu') }}"
        id="storefront-mobile-menu"
    >
        <div x-show="menuOpen" x-transition.opacity class="absolute inset-0 bg-slate-950/50" x-on:click="menuOpen = false"></div>
        <div
            x-show="menuOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="absolute inset-y-0 right-0 flex w-[88%] max-w-sm flex-col bg-white shadow-2xl"
        >
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <span class="text-sm font-extrabold uppercase tracking-wider text-brand-950">{{ __('messages.affiliate_menu') }}</span>
                <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-600" aria-label="{{ __('messages.affiliate_close') }}" x-on:click="menuOpen = false">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto px-5 py-4">
                <nav class="space-y-1" aria-label="{{ __('messages.affiliate_main_nav') }}">
                    <a href="{{ $homeUrl }}" class="flex items-center justify-between rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-800 hover:bg-slate-50">
                        {{ __('messages.home') }}
                    </a>
                    <a href="{{ $productsUrl }}" class="flex items-center justify-between rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-800 hover:bg-slate-50">
                        {{ __('messages.affiliate_all_products') }}
                        <span class="text-xs font-medium text-slate-400">{{ $productCount }}</span>
                    </a>
                    <a href="{{ $homeUrl }}#unggulan" class="flex items-center justify-between rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-800 hover:bg-slate-50">
                        {{ __('messages.affiliate_featured') }}
                    </a>
                    <a href="{{ $homeUrl }}#produk-baru" class="flex items-center justify-between rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-800 hover:bg-slate-50">
                        {{ __('messages.affiliate_new_products') }}
                    </a>
                    <a href="{{ $blogUrl }}" class="flex items-center justify-between rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-800 hover:bg-slate-50">
                        {{ __('messages.blog') }}
                    </a>
                    <a href="{{ $contactUrl }}" class="flex items-center justify-between rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-800 hover:bg-slate-50">
                        {{ __('messages.contact_us') }}
                    </a>
                </nav>

                <p class="mt-6 text-xs font-semibold uppercase tracking-wider text-slate-400">{{ __('messages.affiliate_categories') }}</p>
                <div class="mt-2 space-y-0.5">
                    @forelse ($categories as $category)
                        <a href="{{ affiliate_category_url($locale, $category->slug) }}" class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                            <span class="grid h-8 w-8 place-items-center rounded-lg bg-slate-100 text-brand-800">
                                <x-storefront.category-icon :slug="$category->slug" class="h-4 w-4" />
                            </span>
                            <span class="min-w-0 flex-1 truncate">{{ $category->name }}</span>
                            <span class="text-xs text-slate-400">{{ $category->products_count }}</span>
                        </a>
                    @empty
                        <p class="px-3 py-2 text-sm text-slate-500">{{ __('messages.affiliate_no_categories') }}</p>
                    @endforelse
                </div>

                <div class="mt-6 flex items-center gap-2 border-t border-slate-100 pt-4">
                    @foreach (Language::getActive() as $language)
                        <a href="{{ LocaleSwitchUrl::forLocale($language->code) }}" @class([
                            'rounded-full border px-3 py-1 text-xs font-semibold uppercase',
                            'border-brand-900 bg-brand-900 text-white' => $language->code === $locale,
                            'border-slate-200 text-slate-600' => $language->code !== $locale,
                        ])>{{ $language->code }}</a>
                    @endforeach
                </div>
            </div>

            <div class="border-t border-slate-100 p-5">
                <a href="{{ $productsUrl }}" class="flex w-full items-center justify-center gap-2 rounded-full bg-accent-500 px-5 py-3 text-sm font-semibold text-white">
                    {{ __('messages.affiliate_shop_now') }}
                </a>
            </div>
        </div>
    </div>
</div>
