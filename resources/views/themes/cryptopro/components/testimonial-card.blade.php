@php
    $locale = app()->getLocale();
    $translation = $testimonial->translate($locale, false);
    $content = $translation?->content;
    $authorName = $translation?->author_name;
    $authorPosition = $translation?->author_position;
    $authorCompany = $translation?->author_company;
    $photoUrl = media_url($testimonial->photo_id);
    $rating = max(1, min(5, (int) ($testimonial->rating ?? 5)));
@endphp

@if (filled($content) || filled($authorName))
    <article class="flex h-full flex-col rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        @if (filled($content))
            <blockquote class="flex-1 text-gray-700">&ldquo;{{ $content }}&rdquo;</blockquote>
        @endif

        <div class="mt-5 flex items-center gap-1" aria-label="{{ $rating }}">
            @for ($star = 1; $star <= 5; $star++)
                <span class="{{ $star <= $rating ? 'text-amber-400' : 'text-gray-300' }}" aria-hidden="true">★</span>
            @endfor
        </div>

        <div class="mt-4 flex items-center gap-3">
            @if (filled($photoUrl))
                <img src="{{ $photoUrl }}" alt="{{ $authorName }}" class="h-12 w-12 rounded-full object-cover">
            @endif
            <div>
                @if (filled($authorName))
                    <p class="font-semibold text-gray-900">{{ $authorName }}</p>
                @endif
                @if (filled($authorPosition) || filled($authorCompany))
                    <p class="text-sm text-gray-600">
                        {{ collect([$authorPosition, $authorCompany])->filter()->implode(' · ') }}
                    </p>
                @endif
            </div>
        </div>
    </article>
@endif
