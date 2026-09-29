@extends('layouts.app')

@section('title', __('Galeri'))

@section('content')
    <main class="mx-auto max-w-5xl px-6 py-10">
        <h1 class="mb-8 text-3xl font-bold text-gray-900">{{ __('Galeri') }}</h1>

        @if ($albums->isEmpty())
            <p class="text-gray-600">{{ __('Belum ada album.') }}</p>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($albums as $album)
                    @php
                        $translation = $album->translate($locale, false);
                        $slug = $translation?->slug;
                    @endphp
                    @if ($slug)
                        <a href="{{ route('gallery.show', ['locale' => $locale, 'slug' => $slug]) }}"
                           class="group overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition hover:shadow-md">
                            <div class="aspect-[4/3] overflow-hidden bg-gray-100">
                                @if ($album->cover)
                                    <img src="{{ $album->cover->getUrl('medium') }}"
                                         alt="{{ $translation?->title }}"
                                         class="h-full w-full object-cover transition group-hover:scale-105" />
                                @else
                                    <div class="flex h-full items-center justify-center text-sm text-gray-400">
                                        {{ __('Tanpa cover') }}
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h2 class="text-lg font-semibold text-gray-900 group-hover:text-emerald-700">
                                    {{ $translation?->title }}
                                </h2>
                                @if ($translation?->description)
                                    <p class="mt-1 line-clamp-2 text-sm text-gray-600">{{ $translation->description }}</p>
                                @endif
                                <p class="mt-2 text-xs text-gray-500">{{ $album->media_count }} {{ __('foto') }}</p>
                            </div>
                        </a>
                    @endif
                @endforeach
            </div>
        @endif
    </main>
@endsection
