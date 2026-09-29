@php
    use App\Domain\Language\Models\Language;
    use App\Domain\Language\Support\LocaleSwitchUrl;

    $languages = Language::getActive();
    $currentLocale = app()->getLocale();
@endphp

<div class="flex items-center gap-2">
    @foreach ($languages as $language)
        <a
            href="{{ LocaleSwitchUrl::forLocale($language->code) }}"
            @class([
                'rounded-md px-3 py-1 text-sm font-medium transition',
                'bg-emerald-600 text-white' => $currentLocale === $language->code,
                'bg-gray-200 text-gray-700 hover:bg-gray-300' => $currentLocale !== $language->code,
            ])
        >
            @if ($language->flag)
                <span class="mr-1">{{ $language->flag }}</span>
            @endif
            {{ strtoupper($language->code) }}
        </a>
    @endforeach
</div>
