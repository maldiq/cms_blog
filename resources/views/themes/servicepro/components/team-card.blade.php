@php
    $locale = app()->getLocale();
    $translation = $team->translate($locale, false);
    $name = $translation?->name;
    $position = $translation?->position;
    $photoUrl = media_url($team->photo_id);
    $socialLinks = collect($team->social_links ?? [])->filter(fn ($url) => filled($url));
@endphp

@if (filled($name))
    <article class="group overflow-hidden rounded-xl border border-gray-200 bg-white text-center shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg">
        @if (filled($photoUrl))
            <img src="{{ $photoUrl }}" alt="{{ $name }}" class="aspect-square w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy" decoding="async">
        @else
            <div class="aspect-square w-full bg-gray-100"></div>
        @endif

        <div class="space-y-2 p-5">
            <h3 class="text-lg font-semibold text-gray-900">{{ $name }}</h3>
            @if (filled($position))
                <p class="text-sm text-gray-600">{{ $position }}</p>
            @endif

            @if ($socialLinks->isNotEmpty())
                <ul class="flex flex-wrap justify-center gap-3 pt-2">
                    @foreach ($socialLinks as $network => $url)
                        <li>
                            <a
                                href="{{ $url }}"
                                class="text-xs uppercase tracking-wide text-gray-500 hover:text-gray-900"
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                {{ $network }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </article>
@endif
