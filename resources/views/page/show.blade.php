@extends('layouts.app')

@php
    $translation = $page->translate($locale, false);
    $template = $page->template ?: 'default';
@endphp

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
