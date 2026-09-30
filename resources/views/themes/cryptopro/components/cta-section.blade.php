<section
    class="relative overflow-hidden py-24 lg:py-32"
    @if (filled($backgroundUrl))
        style="background-image: url('{{ $backgroundUrl }}'); background-size: cover; background-position: center;"
    @endif
>
    <div class="absolute inset-0 bg-black/60" aria-hidden="true"></div>

    <div class="relative z-10 mx-auto max-w-4xl px-4 text-center text-white lg:px-6">
        @if (filled($title))
            <h2 class="text-3xl font-bold tracking-tight sm:text-4xl lg:text-5xl">{{ $title }}</h2>
        @endif

        @if (filled($subtitle))
            <p class="mx-auto mt-4 max-w-2xl text-lg text-white/90 sm:text-xl">{{ $subtitle }}</p>
        @endif

        @if (filled($ctaLabel) && filled($ctaUrl))
            <a
                href="{{ $ctaUrl }}"
                class="mt-8 inline-flex rounded-lg px-8 py-3 text-base font-semibold text-white transition hover:opacity-90"
                style="background-color: var(--color-primary, currentColor)"
            >
                {{ $ctaLabel }}
            </a>
        @endif
    </div>
</section>
