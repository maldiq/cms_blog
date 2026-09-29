@extends('layouts.app')

@php
    $translation = $album->translate($locale, false);
    $title = $translation?->title ?? 'Album';
@endphp

@section('title', $title)

@section('content')
    <main class="mx-auto max-w-5xl px-6 py-10">
        <nav class="mb-6 text-sm">
            <a href="{{ route('gallery.index', ['locale' => $locale]) }}" class="text-emerald-700 hover:underline">
                ← {{ __('Galeri') }}
            </a>
        </nav>

        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">{{ $title }}</h1>
            @if ($translation?->description)
                <p class="mt-2 text-gray-600">{{ $translation->description }}</p>
            @endif
        </header>

        @livewire(\App\Domain\Gallery\Album\Livewire\GalleryGrid::class, [
            'albumId' => $album->id,
            'locale' => $locale,
        ])
    </main>
@endsection
