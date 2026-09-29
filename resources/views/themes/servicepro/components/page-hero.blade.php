@if (filled($title) || filled($subtitle))
    <section class="bg-gray-900 py-14 text-white lg:py-16">
        <div class="mx-auto max-w-7xl px-4 text-center lg:px-6">
            @if (filled($title))
                <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">{{ $title }}</h1>
            @endif
            @if (filled($subtitle))
                <p class="mx-auto mt-3 max-w-2xl text-lg text-white/85">{{ $subtitle }}</p>
            @endif
        </div>
    </section>
@endif
