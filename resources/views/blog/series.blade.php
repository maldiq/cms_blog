@extends('layouts.app')

@section('title', $series->translate($locale)?->title ?? 'Series')

@section('content')
    <main class="mx-auto max-w-3xl px-6 py-10">
        @if ($series->cover?->getFullUrl())
            <img src="{{ $series->cover->getFullUrl() }}" alt="" class="mb-6 aspect-[21/9] w-full rounded-xl object-cover" />
        @endif

        <h1 class="text-3xl font-bold">{{ $series->translate($locale)?->title }}</h1>
        <p class="mt-3 text-gray-600">{{ $series->translate($locale)?->description }}</p>

        <ol class="mt-8 space-y-4">
            @foreach ($posts as $index => $post)
                @php $translation = $post->translate($locale, false); @endphp
                <li class="flex gap-4 rounded-lg border border-gray-200 p-4">
                    <span class="text-2xl font-bold text-emerald-600">{{ $index + 1 }}</span>
                    <div>
                        <a href="{{ route('blog.series.post', ['locale' => $locale, 'slug' => $series->translate($locale)?->slug, 'post_slug' => $translation?->slug]) }}"
                           class="text-lg font-semibold text-gray-900 hover:text-emerald-700">
                            {{ $translation?->title }}
                        </a>
                        <p class="text-sm text-gray-600">{{ $translation?->excerpt }}</p>
                    </div>
                </li>
            @endforeach
        </ol>
    </main>
@endsection
