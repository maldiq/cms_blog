@extends('theme::layouts.app')

@section('content')
    <x-theme::page-hero :title="theme_locale('team.title')" :subtitle="theme_locale('team.subtitle')" />

    <section class="py-12 lg:py-16">
        <div class="mx-auto max-w-7xl px-4 lg:px-6">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($teams as $team)
                    @include('theme::components.team-card', ['team' => $team])
                @endforeach
            </div>
        </div>
    </section>
@endsection
