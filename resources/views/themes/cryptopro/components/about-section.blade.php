<section class="bg-gray-50 py-16 lg:py-20">
    <div class="mx-auto grid max-w-7xl grid-cols-1 items-center gap-10 px-4 lg:grid-cols-2 lg:px-6">
        <div>
            @if (filled($imageUrl))
                <img src="{{ $imageUrl }}" alt="{{ $title }}" class="w-full rounded-2xl object-cover shadow-md">
            @endif
        </div>

        <div class="space-y-5">
            @if (filled($title))
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">{{ $title }}</h2>
            @endif

            @if (filled($subtitle))
                <p class="text-lg text-gray-600">{{ $subtitle }}</p>
            @endif

            @if (filled($content))
                <div class="prose prose-emerald max-w-none text-gray-700">
                    {!! $content !!}
                </div>
            @endif

            @if ($points->isNotEmpty())
                <ul class="space-y-3">
                    @foreach ($points as $point)
                        <li class="flex items-start gap-3 text-gray-700">
                            <span class="mt-0.5 inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-xs font-bold text-white" style="background-color: var(--color-primary, currentColor)">✓</span>
                            <span>{{ $point['text'] }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif

            <a
                href="{{ $aboutUrl }}"
                class="inline-flex rounded-lg px-5 py-2.5 text-sm font-semibold text-white transition hover:opacity-90"
                style="background-color: var(--color-primary, currentColor)"
            >
                {{ __('messages.read_more') }}
            </a>
        </div>
    </div>
</section>
