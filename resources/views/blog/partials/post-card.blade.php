@php
    $translation = $post->translate($locale, false);
    $title = $translation?->title ?? 'Post';
    $slug = $translation?->slug ?? $post->id;
    $excerpt = $translation?->excerpt;
    $imageUrl = $post->featuredImage?->getFullUrl('medium') ?? $post->featuredImage?->getFullUrl();
@endphp

<article class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
    @if ($imageUrl)
        <a href="{{ route('blog.show', ['locale' => $locale, 'slug' => $slug]) }}">
            <img src="{{ $imageUrl }}" alt="" class="aspect-video w-full object-cover" loading="lazy" />
        </a>
    @endif
    <div class="space-y-2 p-4">
        <h2 class="text-lg font-semibold text-gray-900">
            <a href="{{ route('blog.show', ['locale' => $locale, 'slug' => $slug]) }}" class="hover:text-emerald-700">
                {{ $title }}
            </a>
        </h2>
        @if ($excerpt)
            <p class="text-sm text-gray-600">{{ $excerpt }}</p>
        @endif
        <div class="flex flex-wrap gap-2 text-xs text-gray-500">
            <span>{{ $post->user?->name }}</span>
            @if ($post->published_at)
                <span>• {{ $post->published_at->translatedFormat('d M Y') }}</span>
            @endif
            @if ($post->reading_time)
                <span>• {{ $post->reading_time }} min</span>
            @endif
        </div>
    </div>
</article>
