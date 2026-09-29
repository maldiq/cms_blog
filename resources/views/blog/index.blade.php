@extends('layouts.app')

@section('title', 'Blog')

@section('content')
    <main class="mx-auto max-w-5xl px-6 py-10">
        <h1 class="mb-6 text-3xl font-bold text-gray-900">Blog</h1>

        @isset($category)
            <p class="mb-4 text-gray-600">{{ $category->translate($locale)?->name }}</p>
        @endisset

        @isset($tag)
            <p class="mb-4 text-gray-600">Tag: {{ $tag->translate($locale)?->name }}</p>
        @endisset

        @livewire(\App\Domain\Blog\Post\Livewire\BlogList::class, [
            'locale' => $locale,
            'category' => request('category', $category?->translate($locale)?->slug ?? null),
            'tag' => request('tag', $tag?->translate($locale)?->slug ?? null),
        ])
    </main>
@endsection
