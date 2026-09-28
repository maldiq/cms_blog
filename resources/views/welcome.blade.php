@extends('layouts.app')

@section('title', 'Beranda — ' . config('app.name'))

@section('content')
    <main class="mx-auto flex min-h-screen max-w-3xl flex-col items-center justify-center px-6 py-16">
        <h1 class="text-4xl font-bold tracking-tight text-emerald-700">
            {{ config('app.name', 'CMS Blog') }}
        </h1>
        <p class="mt-4 text-center text-lg text-gray-600">
            Frontend publik siap. Panel admin tersedia di
            <a href="{{ url('/kelola') }}" class="font-medium text-emerald-600 underline hover:text-emerald-800">
                /kelola
            </a>.
        </p>
    </main>
@endsection
