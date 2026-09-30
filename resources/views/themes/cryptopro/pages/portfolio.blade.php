@extends('theme::layouts.app')

@section('content')
    <x-theme::page-hero :title="theme_locale('portfolio.title')" :subtitle="theme_locale('portfolio.subtitle')" />

    <section class="py-12 lg:py-16">
        <div class="mx-auto max-w-7xl px-4 lg:px-6">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($portfolios as $portfolio)
                    @include('theme::components.portfolio-card', ['portfolio' => $portfolio])
                @endforeach
            </div>

            <div class="mt-10">
                {{ $portfolios->links() }}
            </div>
        </div>
    </section>
@endsection
