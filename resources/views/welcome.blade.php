@extends('layouts.app')

@section('content')
    <main class="mx-auto flex min-h-[calc(100vh-4.5rem)] max-w-3xl flex-col items-center justify-center px-6 py-16">
        <p class="text-sm font-medium uppercase tracking-wide text-emerald-600">
            {{ __('messages.home') }} · {{ __('messages.blog') }}
        </p>
        <h1 class="mt-2 text-4xl font-bold tracking-tight text-emerald-700">
            {{ __('messages.welcome') }}
        </h1>
        <p class="mt-4 text-center text-lg text-gray-600">
            {{ __('messages.public_intro') }}
            <a href="{{ url('/kelola') }}" class="font-medium text-emerald-600 underline hover:text-emerald-800">
                /kelola
            </a>.
        </p>
    </main>
@endsection
