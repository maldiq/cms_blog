@extends('theme::layouts.app')

@section('content')
    <x-theme::page-hero :title="theme_locale('testimonial.title')" :subtitle="theme_locale('testimonial.subtitle')" />

    <section class="py-12 lg:py-16">
        <div class="mx-auto max-w-7xl px-4 lg:px-6">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($testimonials as $testimonial)
                    @include('theme::components.testimonial-card', ['testimonial' => $testimonial])
                @endforeach
            </div>
        </div>
    </section>
@endsection
