@php
    $homeUrl = url('/' . app()->getLocale());
    $logoUrl = media_url(theme('branding.logo_id')) ?? media_url(theme('branding.logo_dark_id'));
    $siteName = setting('site_name');
    $aboutText = theme_locale('footer.about_text');
    $copyrightText = theme_locale('footer.copyright');
@endphp

<footer class="border-t border-gray-200 bg-gray-900 text-gray-300">
    <div class="mx-auto max-w-7xl px-4 py-12 lg:px-6">
        <div class="grid grid-cols-1 gap-10 md:grid-cols-2 lg:grid-cols-4">
            <div class="space-y-4">
                <a href="{{ $homeUrl }}" class="inline-flex items-center">
                    @if (filled($logoUrl))
                        <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="h-9 w-auto max-w-[160px] object-contain brightness-0 invert">
                    @elseif (filled($siteName))
                        <span class="text-lg font-semibold text-white">{{ $siteName }}</span>
                    @endif
                </a>

                @if (filled($aboutText))
                    <p class="text-sm leading-relaxed text-gray-400">{{ $aboutText }}</p>
                @endif

                @if ($socialLinks->isNotEmpty())
                    <ul class="flex flex-wrap gap-3">
                        @foreach ($socialLinks as $social)
                            <li>
                                <a
                                    href="{{ $social['url'] }}"
                                    class="text-sm uppercase tracking-wide text-gray-400 hover:text-white"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    {{ $social['key'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            @foreach ($footerColumns as $column)
                @php
                    $columnTitle = \App\Support\Theme\ThemeValue::localize($column['title'] ?? null);
                    $menuLocation = $column['menu_location'] ?? null;
                @endphp
                @if (filled($columnTitle) || filled($menuLocation))
                    <div class="space-y-3">
                        @if (filled($columnTitle))
                            <h3 class="text-sm font-semibold uppercase tracking-wide text-white">{{ $columnTitle }}</h3>
                        @endif
                        @if (filled($menuLocation))
                            <div class="[&_a]:text-gray-400 [&_a:hover]:text-white [&_ul]:flex-col [&_ul]:items-start [&_ul]:gap-2">
                                <x-menu :location="$menuLocation" />
                            </div>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <div class="border-t border-gray-800">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-6 text-sm text-gray-500 sm:flex-row sm:items-center sm:justify-between lg:px-6">
            <p>
                @if (filled($copyrightText))
                    {{ $copyrightText }}
                @endif
                @if (filled($copyrightText))
                    ·
                @endif
                {{ date('Y') }}
            </p>
            <div class="[&_a]:text-gray-400 [&_a:hover]:text-white">
                <x-menu location="footer-bottom" />
            </div>
        </div>
    </div>
</footer>
