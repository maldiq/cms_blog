@php
    $values = theme('about_page.values');
    $valueItems = is_array($values) ? $values : [];
@endphp

<section class="bg-white py-16 lg:py-20">
    <div class="mx-auto max-w-7xl space-y-16 px-4 lg:px-6">
        <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
            @if (filled(theme_locale('about_page.vision')))
                <div class="rounded-xl border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-900">{{ __('messages.vision') }}</h2>
                    <p class="mt-3 text-gray-600">{{ theme_locale('about_page.vision') }}</p>
                </div>
            @endif
            @if (filled(theme_locale('about_page.mission')))
                <div class="rounded-xl border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-900">{{ __('messages.mission') }}</h2>
                    <p class="mt-3 text-gray-600">{{ theme_locale('about_page.mission') }}</p>
                </div>
            @endif
        </div>

        @if (filled(theme_locale('about_page.history')))
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ __('messages.history') }}</h2>
                <div class="prose prose-emerald mt-4 max-w-none text-gray-700">
                    {!! theme_locale('about_page.history') !!}
                </div>
            </div>
        @endif

        @if ($valueItems !== [])
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ __('messages.values') }}</h2>
                <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($valueItems as $item)
                        @php
                            $itemTitle = \App\Support\Theme\ThemeValue::localize($item['title'] ?? null);
                            $itemText = \App\Support\Theme\ThemeValue::localize($item['text'] ?? null);
                        @endphp
                        @if (filled($itemTitle) || filled($itemText))
                            <div class="rounded-xl bg-gray-50 p-5">
                                @if (filled($itemTitle))
                                    <h3 class="font-semibold text-gray-900">{{ $itemTitle }}</h3>
                                @endif
                                @if (filled($itemText))
                                    <p class="mt-2 text-sm text-gray-600">{{ $itemText }}</p>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
