@extends(theme_layout())

@section('content')
    <main class="mx-auto max-w-4xl px-4 py-12 lg:px-6">
        <h1 class="text-3xl font-bold text-gray-900">{{ $translation?->title }}</h1>

        @if (filled($translation?->excerpt))
            <p class="mt-3 text-lg text-gray-600">{{ $translation->excerpt }}</p>
        @endif

        @if (filled($translation?->content))
            <div class="prose prose-emerald mt-8 max-w-none">
                {!! $translation->content !!}
            </div>
        @endif
    </main>
@endsection
