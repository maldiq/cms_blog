<section class="bg-gray-50 py-16 lg:py-20">
    <div class="mx-auto max-w-7xl px-4 lg:px-6">
        @if (filled($title) || filled($subtitle))
            <div class="mx-auto mb-10 max-w-3xl text-center">
                @if (filled($title))
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">{{ $title }}</h2>
                @endif
                @if (filled($subtitle))
                    <p class="mt-3 text-lg text-gray-600">{{ $subtitle }}</p>
                @endif
            </div>
        @endif

        @if ($plans->isNotEmpty())
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                @foreach ($plans as $plan)
                    <article @class([
                        'relative flex h-full flex-col rounded-2xl border bg-white p-6 shadow-sm',
                        'border-2 shadow-md' => (bool) ($plan['is_popular'] ?? false),
                        'border-gray-200' => ! ($plan['is_popular'] ?? false),
                    ]) @if ($plan['is_popular'] ?? false) style="border-color: var(--color-primary, currentColor)" @endif>
                        @if ($plan['is_popular'] ?? false)
                            <span
                                class="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full px-3 py-1 text-xs font-semibold text-white"
                                style="background-color: var(--color-primary, currentColor)"
                            >
                                {{ __('messages.popular') }}
                            </span>
                        @endif

                        @if (filled($plan['name']))
                            <h3 class="text-xl font-semibold text-gray-900">{{ $plan['name'] }}</h3>
                        @endif

                        @if (filled($plan['price']))
                            <p class="mt-4 text-4xl font-bold text-gray-900">
                                {{ $plan['price'] }}
                                @if (filled($plan['period']))
                                    <span class="text-base font-normal text-gray-500">/ {{ $plan['period'] }}</span>
                                @endif
                            </p>
                        @endif

                        @if (! empty($plan['features']))
                            <ul class="mt-6 flex-1 space-y-2 text-sm text-gray-600">
                                @foreach ($plan['features'] as $feature)
                                    <li class="flex items-start gap-2">
                                        <span class="mt-0.5 text-emerald-600" aria-hidden="true">✓</span>
                                        <span>{{ $feature }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        @if (filled($plan['cta_label']) && filled($plan['cta_url']))
                            <a
                                href="{{ $plan['cta_url'] }}"
                                @class([
                                    'mt-8 inline-flex justify-center rounded-lg px-4 py-2.5 text-sm font-semibold transition hover:opacity-90',
                                    'text-white' => (bool) ($plan['is_popular'] ?? false),
                                    'border border-gray-300 text-gray-900' => ! ($plan['is_popular'] ?? false),
                                ])
                                @if ($plan['is_popular'] ?? false) style="background-color: var(--color-primary, currentColor)" @endif
                            >
                                {{ $plan['cta_label'] }}
                            </a>
                        @endif
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
