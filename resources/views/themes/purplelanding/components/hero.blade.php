@php
    $hasBackgroundImage = filled($backgroundUrl);
    $leadFormTitle = theme_locale('hero.lead_form_title') ?? __('messages.hero_lead_title');
@endphp

<section
    id="top"
    class="theme-hero-fade theme-hero-purple relative flex min-h-[85vh] w-full scroll-mt-0 items-center overflow-hidden"
    @if ($hasBackgroundImage)
        style="background-image: url('{{ $backgroundUrl }}'); background-size: cover; background-position: center;"
    @endif
>
    @if ($hasBackgroundImage)
        <div class="absolute inset-0 bg-violet-950/75" aria-hidden="true"></div>
    @endif

    <div class="pointer-events-none absolute -right-20 top-20 h-72 w-72 rounded-full bg-white/10 blur-3xl" aria-hidden="true"></div>
    <div class="pointer-events-none absolute -left-16 bottom-10 h-64 w-64 rounded-full bg-fuchsia-400/20 blur-3xl" aria-hidden="true"></div>

    <div class="relative z-10 mx-auto w-full max-w-7xl px-4 py-16 lg:px-6 lg:py-24">
        <div class="grid grid-cols-1 items-center gap-12 lg:grid-cols-2">
            <div class="space-y-6 text-white">
                @if (filled($title))
                    <h1 class="text-4xl font-bold leading-tight tracking-tight sm:text-5xl lg:text-[3.25rem]">{{ $title }}</h1>
                @endif

                @if (filled($subtitle))
                    <p class="max-w-xl text-lg text-violet-100/95 sm:text-xl">{{ $subtitle }}</p>
                @endif

                @php
                    $heroCtaHref = filled($ctaUrl) && str_starts_with((string) $ctaUrl, '#') ? $ctaUrl : '#contact';
                @endphp
                @if (filled($ctaLabel))
                    <a
                        href="{{ $heroCtaHref }}"
                        class="inline-flex rounded-full border-2 border-white/30 bg-white/10 px-8 py-3 text-base font-semibold text-white backdrop-blur transition hover:bg-white/20"
                    >
                        {{ $ctaLabel }}
                    </a>
                @endif
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-2xl shadow-violet-900/25 sm:p-8">
                <h2 class="text-xl font-semibold text-gray-900">{{ $leadFormTitle }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('messages.hero_lead_subtitle') }}</p>
                <div class="mt-6">
                    @livewire(\App\Domain\Contact\Livewire\HeroLeadForm::class)
                </div>
            </div>
        </div>
    </div>
</section>

@once
    @push('styles')
        <style>
            @keyframes themeHeroFadeIn {
                from { opacity: 0; transform: translateY(12px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .theme-hero-fade { animation: themeHeroFadeIn 0.8s ease-out both; }
        </style>
    @endpush
@endonce
