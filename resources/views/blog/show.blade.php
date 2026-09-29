@extends('layouts.app')

@php
    $translation = $post->translate($locale, false);
    $title = $translation?->title ?? 'Post';
    $metaTitle = $translation?->meta_title ?: $title;
    $metaDescription = $translation?->meta_description ?: $translation?->excerpt;
    $imageUrl = $post->featuredImage?->getFullUrl();
@endphp

@push('meta')
    <meta name="description" content="{{ $metaDescription }}" />
    <meta property="og:title" content="{{ $metaTitle }}" />
    <meta property="og:description" content="{{ $metaDescription }}" />
    @if ($imageUrl)
        <meta property="og:image" content="{{ $imageUrl }}" />
    @endif
@endpush

@section('title', $metaTitle)

@section('content')
    <main class="mx-auto max-w-3xl px-6 py-10">
        @if (! empty($series) && ! empty($seriesPosts))
            <nav class="mb-6 rounded-lg bg-gray-50 p-4 text-sm">
                <p class="font-medium">{{ $series->translate($locale)?->title }}</p>
                <ol class="mt-2 list-decimal space-y-1 pl-5">
                    @foreach ($seriesPosts as $index => $seriesPost)
                        @php $seriesTranslation = $seriesPost->translate($locale, false); @endphp
                        <li>
                            <a href="{{ route('blog.series.post', ['locale' => $locale, 'slug' => $series->translate($locale)?->slug, 'post_slug' => $seriesTranslation?->slug]) }}"
                               class="{{ $seriesPost->id === $post->id ? 'font-semibold text-emerald-700' : 'text-gray-700 hover:text-emerald-700' }}">
                                {{ $index + 1 }}. {{ $seriesTranslation?->title }}
                            </a>
                        </li>
                    @endforeach
                </ol>
            </nav>
        @endif

        <article>
            @if ($imageUrl)
                <img src="{{ $imageUrl }}" alt="" class="mb-6 aspect-video w-full rounded-xl object-cover" />
            @endif

            <h1 class="text-3xl font-bold text-gray-900">{{ $title }}</h1>

            <div class="mt-2 flex flex-wrap gap-2 text-sm text-gray-500">
                <span>{{ $post->user?->name }}</span>
                @if ($post->published_at)
                    <span>• {{ $post->published_at->translatedFormat('d M Y') }}</span>
                @endif
                @if ($post->reading_time)
                    <span>• {{ $post->reading_time }} min read</span>
                @endif
            </div>

            <div class="prose prose-emerald mt-8 max-w-none">
                {!! $translation?->content !!}
            </div>
        </article>

        @if (! empty($previousSeriesPost) || ! empty($nextSeriesPost))
            <div class="mt-10 flex justify-between gap-4 border-t pt-6 text-sm">
                <div>
                    @if (! empty($previousSeriesPost))
                        @php $prevT = $previousSeriesPost->translate($locale, false); @endphp
                        <a href="{{ route('blog.series.post', ['locale' => $locale, 'slug' => $series->translate($locale)?->slug, 'post_slug' => $prevT?->slug]) }}" class="text-emerald-700">← {{ $prevT?->title }}</a>
                    @endif
                </div>
                <div class="text-right">
                    @if (! empty($nextSeriesPost))
                        @php $nextT = $nextSeriesPost->translate($locale, false); @endphp
                        <a href="{{ route('blog.series.post', ['locale' => $locale, 'slug' => $series->translate($locale)?->slug, 'post_slug' => $nextT?->slug]) }}" class="text-emerald-700">{{ $nextT?->title }} →</a>
                    @endif
                </div>
            </div>
        @endif

        @if ($related->isNotEmpty())
            <section class="mt-12">
                <h2 class="mb-4 text-xl font-semibold">Related posts</h2>
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach ($related as $relatedPost)
                        @include('blog.partials.post-card', ['post' => $relatedPost, 'locale' => $locale])
                    @endforeach
                </div>
            </section>
        @endif

        @if ($post->allow_comment)
            @livewire(\App\Domain\Comment\Livewire\CommentSection::class, [
                'commentableType' => \App\Domain\Blog\Post\Models\Post::class,
                'commentableId' => $post->id,
            ])
        @endif
    </main>
@endsection
