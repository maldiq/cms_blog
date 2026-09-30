@extends('theme::layouts.app')

@section('content')
    <x-theme::page-hero :title="theme_locale('services.title')" :subtitle="theme_locale('services.subtitle')" />

    <section class="py-12 lg:py-16">
        <div class="mx-auto max-w-7xl px-4 lg:px-6">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($services as $service)
                    @include('theme::components.service-card', ['service' => $service])
                @endforeach
            </div>

            <div class="mt-10">
                {{ $services->links() }}
            </div>
        </div>
    </section>
@endsection
