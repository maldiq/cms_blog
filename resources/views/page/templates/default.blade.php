<main class="mx-auto max-w-3xl px-6 py-10">
    <h1 class="text-3xl font-bold text-gray-900">{{ $translation?->title }}</h1>
    <article class="prose prose-emerald mt-8 max-w-none">
        {!! $translation?->content !!}
    </article>
</main>
