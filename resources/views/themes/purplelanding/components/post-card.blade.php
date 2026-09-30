@php
    $locale = app()->getLocale();
    $translation = $post->translate($locale, false);
    $title = $translation?->title;
    $slug = $translation?->slug;
    $excerpt = $translation?->excerpt;
    $imageUrl = $post->featuredImage?->getFullUrl('medium') ?? $post->featuredImage?->getFullUrl();
    $postUrl = filled($slug) ? route('blog.show', ['locale' => $locale, 'slug' => $slug]) : null;
@endphp

@if (filled($title) && filled($postUrl))
    <article class="group overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg">
        @if (filled($imageUrl))
            <a href="{{ $postUrl }}">
                <img src="{{ $imageUrl }}" alt="{{ $title }}" class="aspect-video w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
            </a>
        @endif
        <div class="space-y-2 p-5">
            <h3 class="text-lg font-semibold text-gray-900">
                <a href="{{ $postUrl }}" class="hover:opacity-80" style="color: inherit">{{ $title }}</a>
            </h3>
            @if (filled($excerpt))
                <p class="text-sm text-gray-600">{{ $excerpt }}</p>
            @endif
            <div class="flex flex-wrap gap-2 text-xs text-gray-500">
                @if (filled($post->user?->name))
                    <span>{{ $post->user->name }}</span>
                @endif
                @if ($post->published_at)
                    <span>• {{ $post->published_at->translatedFormat('d M Y') }}</span>
                @endif
            </div>
        </div>
    </article>
@endif
