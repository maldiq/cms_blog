@extends('layouts.app')

@section('title', __('messages.blog'))

@section('content')
    @php
        $categoryName = isset($category) ? $category->translate($locale)?->name : null;
        $tagName = isset($tag) ? $tag->translate($locale)?->name : null;
        $activeName = $categoryName ?? $tagName ?? __('messages.affiliate_blog_title');
    @endphp

    <section class="border-b border-slate-200 bg-white">
        <div class="container-page py-7 sm:py-9">
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-xs text-slate-500">
                <a href="{{ route('home', ['locale' => $locale]) }}" class="transition hover:text-brand-800">{{ __('messages.home') }}</a>
                <svg class="h-3.5 w-3.5 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
                @if ($categoryName || $tagName)
                    <a href="{{ route('blog.index', ['locale' => $locale]) }}" class="transition hover:text-brand-800">{{ __('messages.blog') }}</a>
                    <svg class="h-3.5 w-3.5 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                @endif
                <span class="font-medium text-slate-700">{{ $activeName }}</span>
            </nav>

            <div class="mt-5">
                <p class="section-eyebrow">{{ __('messages.affiliate_blog_eyebrow') }}</p>
                <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ $activeName }}</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">{{ __('messages.blog_page_intro') }}</p>
            </div>
        </div>
    </section>

    <div class="container-page py-8 sm:py-10">
        @livewire(\App\Domain\Blog\Post\Livewire\BlogList::class, [
            'locale' => $locale,
            'category' => request('category', isset($category) ? $category->translate($locale)?->slug : null),
            'tag' => request('tag', isset($tag) ? $tag->translate($locale)?->slug : null),
        ])
    </div>
@endsection
