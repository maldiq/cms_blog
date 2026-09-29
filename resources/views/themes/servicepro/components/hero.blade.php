@php
    $hasBackgroundImage = filled($backgroundUrl);
    $gradientStyle = filled($primaryColor)
        ? "background-image: linear-gradient(135deg, {$primaryColor}, color-mix(in srgb, {$primaryColor} 65%, #000));"
        : '';
@endphp

<section
    class="theme-hero-fade relative flex min-h-[80vh] w-full items-center overflow-hidden"
    @if ($hasBackgroundImage)
        style="background-image: url('{{ $backgroundUrl }}'); background-size: cover; background-position: center;"
    @elseif (filled($gradientStyle))
        style="{{ $gradientStyle }}"
    @endif
>
    <div class="absolute inset-0 bg-black/55" aria-hidden="true"></div>

    <div class="relative z-10 mx-auto w-full max-w-7xl px-4 py-20 lg:px-6">
        <div class="max-w-3xl space-y-6 text-white">
            @if (filled($title))
                <h1 class="text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl">{{ $title }}</h1>
            @endif

            @if (filled($subtitle))
                <p class="text-lg text-white/90 sm:text-xl">{{ $subtitle }}</p>
            @endif

            @if (filled($ctaLabel) && filled($ctaUrl))
                <a
                    href="{{ $ctaUrl }}"
                    class="inline-flex rounded-lg px-6 py-3 text-base font-semibold text-white transition hover:opacity-90"
                    style="background-color: var(--color-primary, {{ $primaryColor ?? 'currentColor' }})"
                >
                    {{ $ctaLabel }}
                </a>
            @endif

            @if ($features->isNotEmpty())
                <ul class="mt-8 grid gap-4 sm:grid-cols-2">
                    @foreach ($features as $feature)
                        <li class="flex items-start gap-3 text-sm text-white/95 sm:text-base">
                            @if (filled($feature['icon']))
                                <span class="mt-0.5 shrink-0 text-lg" aria-hidden="true">{{ $feature['icon'] }}</span>
                            @endif
                            @if (filled($feature['text']))
                                <span>{{ $feature['text'] }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</section>

@once
    @push('styles')
        <style>
            @keyframes themeHeroFadeIn {
                from {
                    opacity: 0;
                    transform: translateY(12px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .theme-hero-fade {
                animation: themeHeroFadeIn 0.8s ease-out both;
            }
        </style>
    @endpush
@endonce
