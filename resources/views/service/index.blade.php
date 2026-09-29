@extends(theme_layout())

@section('content')
    <main class="mx-auto max-w-7xl px-4 py-12 lg:px-6">
        @if (filled(theme_locale('services.title')))
            <h1 class="text-3xl font-bold text-gray-900">{{ theme_locale('services.title') }}</h1>
        @endif

        <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($services as $service)
                @include('theme::components.service-card', ['service' => $service])
            @endforeach
        </div>
    </main>
@endsection
