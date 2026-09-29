<main class="mx-auto max-w-3xl px-6 py-10">
    <h1 class="text-3xl font-bold text-gray-900">{{ $translation?->title }}</h1>
    <p class="mt-2 text-gray-600">{{ __('Hubungi kami melalui informasi berikut.') }}</p>
    <article class="prose prose-emerald mt-8 max-w-none">
        {!! $translation?->content !!}
    </article>
    <div class="mt-8 rounded-lg border border-gray-200 bg-gray-50 p-4 text-sm text-gray-700">
        <p class="font-medium">{{ config('app.name') }}</p>
        <p class="mt-1">{{ setting('site_description') }}</p>
    </div>
</main>
