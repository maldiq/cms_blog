@php
    $locale = app()->getLocale();
    $homeUrl = route('home', ['locale' => $locale]);
    $productsUrl = route('products.index', ['locale' => $locale]);
    $blogUrl = route('blog.index', ['locale' => $locale]);
    $contactUrl = route('contact', ['locale' => $locale]);
@endphp

<footer class="mt-20 border-t border-slate-200 bg-white">
    <div class="container-page grid gap-10 py-14 md:grid-cols-2 lg:grid-cols-[1.5fr_1fr_1fr_1.2fr]">
        <div>
            <a href="{{ $homeUrl }}" class="inline-flex items-center gap-2.5">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br from-brand-700 to-brand-950 text-white shadow-md shadow-brand-900/20">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12A1.125 1.125 0 0 1 19.751 22H4.249a1.125 1.125 0 0 1-1.118-1.493l1.263-12A1.125 1.125 0 0 1 5.512 7.5h12.976a1.125 1.125 0 0 1 1.118.993Z" />
                    </svg>
                </span>
                <span class="text-base font-extrabold tracking-tight text-brand-950">{{ $siteName }}</span>
            </a>
            <p class="mt-4 max-w-sm text-sm leading-6 text-slate-600">
                {{ $siteDescription ?: __('messages.affiliate_footer_description') }}
            </p>

            @if ($socials !== [])
                <div class="mt-5 flex flex-wrap items-center gap-2">
                    @foreach ($socials as $network => $url)
                        <a
                            href="{{ $url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="grid h-9 w-9 place-items-center rounded-full border border-slate-200 text-slate-500 transition hover:border-brand-300 hover:bg-brand-950 hover:text-white"
                            aria-label="{{ ucfirst($network) }}"
                        >
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                @switch($network)
                                    @case('facebook')
                                        <path d="M13.5 21v-7.5h2.6l.4-3h-3V8.6c0-.9.25-1.5 1.5-1.5h1.6V4.4c-.3 0-1.3-.1-2.4-.1-2.3 0-3.9 1.4-3.9 4v2.2H7.5v3h2.6V21h3.4Z" />
                                        @break
                                    @case('instagram')
                                        <path fill="none" stroke="currentColor" stroke-width="1.7" d="M8 3.8h8A4.2 4.2 0 0 1 20.2 8v8a4.2 4.2 0 0 1-4.2 4.2H8A4.2 4.2 0 0 1 3.8 16V8A4.2 4.2 0 0 1 8 3.8Z" />
                                        <circle cx="12" cy="12" r="3.2" fill="none" stroke="currentColor" stroke-width="1.7" />
                                        <circle cx="16.7" cy="7.3" r="1.1" />
                                        @break
                                    @case('tiktok')
                                        <path d="M16.6 3c.4 2.1 1.8 3.6 3.9 3.9v2.7c-1.5 0-2.9-.5-3.9-1.3v5.6a5.9 5.9 0 1 1-5.9-5.9c.3 0 .6 0 .9.1v2.8a3.1 3.1 0 1 0 2.2 3V3h2.8Z" />
                                        @break
                                    @case('youtube')
                                        <path d="M21 8.2a3 3 0 0 0-2.1-2.1C17.4 5.6 12 5.6 12 5.6s-5.4 0-6.9.5A3 3 0 0 0 3 8.2 31 31 0 0 0 2.6 12 31 31 0 0 0 3 15.8a3 3 0 0 0 2.1 2.1c1.5.5 6.9.5 6.9.5s5.4 0 6.9-.5a3 3 0 0 0 2.1-2.1 31 31 0 0 0 .4-3.8 31 31 0 0 0-.4-3.8ZM10.2 14.8V9.2L14.9 12l-4.7 2.8Z" />
                                        @break
                                    @case('twitter')
                                        <path d="M4 3h4.3l4.1 5.6L17.2 3H21l-6.4 8.2L21 21h-4.3l-4.4-6-4.8 6H4l6.7-8.4L4 3Z" />
                                        @break
                                    @case('linkedin')
                                        <path d="M6.5 9.5H3.8V21h2.7V9.5ZM5.1 3.5A1.7 1.7 0 1 0 5.1 7a1.7 1.7 0 0 0 0-3.5ZM20.2 14.4c0-3-1.6-4.5-3.8-4.5a3.3 3.3 0 0 0-3 1.6V9.5H10.7V21h2.7v-6c0-1.6.8-2.5 2-2.5s1.9.9 1.9 2.5v6h2.9v-6.6Z" />
                                        @break
                                    @default
                                        <circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="1.7" />
                                @endswitch
                            </svg>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <nav aria-label="{{ __('messages.affiliate_explore') }}">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.affiliate_explore') }}</p>
            <ul class="mt-4 space-y-2.5 text-sm text-slate-600">
                <li><a href="{{ $homeUrl }}" class="transition hover:text-accent-600">{{ __('messages.home') }}</a></li>
                <li><a href="{{ $productsUrl }}" class="transition hover:text-accent-600">{{ __('messages.affiliate_all_products') }}</a></li>
                <li><a href="{{ $homeUrl }}#unggulan" class="transition hover:text-accent-600">{{ __('messages.affiliate_featured') }}</a></li>
                <li><a href="{{ $homeUrl }}#produk-baru" class="transition hover:text-accent-600">{{ __('messages.affiliate_new_products') }}</a></li>
                <li><a href="{{ $blogUrl }}" class="transition hover:text-accent-600">{{ __('messages.blog') }}</a></li>
                <li><a href="{{ $contactUrl }}" class="transition hover:text-accent-600">{{ __('messages.contact_us') }}</a></li>
            </ul>
        </nav>

        <nav aria-label="{{ __('messages.affiliate_categories') }}">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.affiliate_categories') }}</p>
            <ul class="mt-4 space-y-2.5 text-sm text-slate-600">
                @forelse ($categories as $category)
                    <li>
                        <a href="{{ affiliate_category_url($locale, $category->slug) }}" class="transition hover:text-accent-600">
                            {{ $category->name }}
                        </a>
                    </li>
                @empty
                    <li class="text-slate-500">{{ __('messages.affiliate_no_categories') }}</li>
                @endforelse
                <li><a href="{{ $productsUrl }}" class="font-semibold text-brand-800 transition hover:text-accent-600">{{ __('messages.affiliate_all_categories') }}</a></li>
            </ul>
        </nav>

        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('messages.affiliate_disclosure_title') }}</p>
            <p class="mt-4 text-sm leading-6 text-slate-600">
                {{ __('messages.affiliate_disclosure_short') }}
            </p>
            <p class="mt-3 text-xs leading-5 text-slate-500">
                {{ __('messages.affiliate_price_note') }}
            </p>
            <a href="{{ $productsUrl }}" class="mt-5 inline-flex items-center gap-2 rounded-full bg-brand-950 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-brand-800">
                {{ __('messages.affiliate_shop_now') }}
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </a>
        </div>
    </div>

    <div class="border-t border-slate-100 bg-slate-50">
        <div class="container-page flex flex-col gap-2 py-5 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ date('Y') }} {{ $siteName }}. {{ __('messages.affiliate_rights') }}</p>
            <p class="flex flex-wrap items-center gap-x-3 gap-y-1">
                <a href="{{ $blogUrl }}" class="transition hover:text-slate-800">{{ __('messages.blog') }}</a>
                <span aria-hidden="true" class="text-slate-300">·</span>
                <a href="{{ $contactUrl }}" class="transition hover:text-slate-800">{{ __('messages.contact_us') }}</a>
                <span aria-hidden="true" class="text-slate-300">·</span>
                <span>{{ __('messages.affiliate_footer_credit') }}</span>
            </p>
        </div>
    </div>
</footer>
