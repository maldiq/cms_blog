@extends('theme::layouts.app')

@section('content')
    <x-theme::page-hero :title="theme_locale('about.title')" :subtitle="theme_locale('about.subtitle')" />
    <x-theme::about-section />
    @include('theme::components.about-page-sections')
@endsection
