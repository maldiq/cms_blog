<section id="about" class="scroll-mt-24 bg-gray-50 py-16 lg:py-20">
    <div class="mx-auto grid max-w-7xl grid-cols-1 items-center gap-10 px-4 lg:grid-cols-2 lg:px-6">
        <div class="order-2 lg:order-1">
            @if (filled($imageUrl))
                <img src="{{ $imageUrl }}" alt="{{ $title }}" class="w-full rounded-2xl object-cover shadow-lg ring-1 ring-violet-100">
            @else
                <div class="aspect-[4/3] w-full rounded-2xl bg-gradient-to-br from-violet-600 to-fuchsia-500 shadow-lg" aria-hidden="true"></div>
            @endif
        </div>

        <div class="order-1 space-y-5 lg:order-2">
            <p class="text-sm font-semibold uppercase tracking-wider text-violet-600">{{ __('messages.purple_about_eyebrow') }}</p>

            @if (filled($title))
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">{{ $title }}</h2>
            @endif

            @if (filled($subtitle))
                <p class="text-lg text-gray-600">{{ $subtitle }}</p>
            @endif

            @if (filled($content))
                <div class="prose prose-violet max-w-none text-gray-700">
                    {!! $content !!}
                </div>
            @endif

            @if ($points->isNotEmpty())
                <ul class="space-y-3">
                    @foreach ($points as $point)
                        <li class="flex items-start gap-3 text-gray-700">
                            <span class="mt-0.5 inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-violet-600 text-xs font-bold text-white">✓</span>
                            <span>{{ $point['text'] }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif

            <a
                href="#contact"
                class="inline-flex rounded-full bg-violet-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-violet-600/20 transition hover:bg-violet-700"
            >
                {{ __('messages.contact_us') }}
            </a>
        </div>
    </div>
</section>
