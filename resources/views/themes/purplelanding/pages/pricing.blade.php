@extends('theme::layouts.app')

@section('content')
    <x-theme::page-hero :title="theme_locale('pricing.title')" :subtitle="theme_locale('pricing.subtitle')" />
    <x-theme::pricing-section />
    <x-theme::faq-section />
@endsection
