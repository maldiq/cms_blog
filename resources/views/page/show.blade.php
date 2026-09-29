@extends('layouts.app')

@php
    $translation = $page->translate($locale, false);
    $title = $translation?->title ?? 'Page';
    $metaTitle = $translation?->meta_title ?: $title;
    $metaDescription = $translation?->meta_description;
    $template = $page->template ?: 'default';
@endphp

@push('meta')
    @if ($metaDescription)
        <meta name="description" content="{{ $metaDescription }}" />
    @endif
    <meta property="og:title" content="{{ $metaTitle }}" />
    @if ($metaDescription)
        <meta property="og:description" content="{{ $metaDescription }}" />
    @endif
@endpush

@section('title', $metaTitle)

@section('content')
    @includeFirst([
        'page.templates.' . $template,
        'page.templates.default',
    ], [
        'page' => $page,
        'locale' => $locale,
        'translation' => $translation,
    ])
@endsection
