<section class="bg-white py-16 lg:py-20">
    <div class="mx-auto max-w-7xl px-4 lg:px-6">
        @if (filled($title) || filled($subtitle))
            <div class="mx-auto mb-12 max-w-3xl text-center">
                @if (filled($title))
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">{{ $title }}</h2>
                @endif
                @if (filled($subtitle))
                    <p class="mt-3 text-lg text-gray-600">{{ $subtitle }}</p>
                @endif
            </div>
        @endif

        @if ($steps->isNotEmpty())
            <ol class="theme-process-steps grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-4">
                @foreach ($steps as $step)
                    <li class="theme-process-step relative rounded-xl border border-gray-200 bg-gray-50 p-6 text-center">
                        @if (filled($step['number']))
                            <p class="text-4xl font-bold" style="color: var(--color-primary, currentColor)">{{ $step['number'] }}</p>
                        @endif
                        @if (filled($step['title']))
                            <h3 class="mt-3 text-lg font-semibold text-gray-900">{{ $step['title'] }}</h3>
                        @endif
                        @if (filled($step['description']))
                            <p class="mt-2 text-sm text-gray-600">{{ $step['description'] }}</p>
                        @endif
                    </li>
                @endforeach
            </ol>
        @endif
    </div>
</section>

@once
    @push('styles')
        <style>
            @media (min-width: 1024px) {
                .theme-process-step:not(:last-child)::after {
                    content: '';
                    position: absolute;
                    top: 50%;
                    right: -1rem;
                    width: 2rem;
                    height: 2px;
                    background-color: color-mix(in srgb, var(--color-primary) 70%, transparent);
                    transform: translateY(-50%);
                }
            }
        </style>
    @endpush
@endonce
