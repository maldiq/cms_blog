<section id="pricing" class="scroll-mt-24 bg-white py-16 lg:py-20">
    <div class="mx-auto max-w-7xl px-4 lg:px-6">
        @if (filled($title) || filled($subtitle))
            <div class="mx-auto mb-12 max-w-3xl text-center">
                <p class="text-sm font-semibold uppercase tracking-wider text-violet-600">{{ __('messages.purple_pricing_eyebrow') }}</p>
                @if (filled($title))
                    <h2 class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">{{ $title }}</h2>
                @endif
                @if (filled($subtitle))
                    <p class="mt-3 text-lg text-gray-600">{{ $subtitle }}</p>
                @endif
            </div>
        @endif

        @if ($plans->isNotEmpty())
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                @foreach ($plans as $plan)
                    <article @class([
                        'relative flex h-full flex-col rounded-2xl border p-8',
                        'border-violet-600 bg-violet-50 shadow-xl ring-2 ring-violet-600' => (bool) ($plan['is_popular'] ?? false),
                        'border-violet-100 bg-white shadow-sm' => ! ($plan['is_popular'] ?? false),
                    ])>
                        @if ($plan['is_popular'] ?? false)
                            <span class="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full bg-violet-600 px-3 py-1 text-xs font-semibold text-white">
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
                            <ul class="mt-6 flex-1 space-y-3 text-sm text-gray-600">
                                @foreach ($plan['features'] as $feature)
                                    <li class="flex items-start gap-2">
                                        <span class="mt-0.5 text-violet-600" aria-hidden="true">✓</span>
                                        <span>{{ $feature }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        <a
                            href="#contact"
                            @class([
                                'mt-8 inline-flex justify-center rounded-full px-4 py-3 text-sm font-semibold transition',
                                'bg-violet-600 text-white hover:bg-violet-700' => (bool) ($plan['is_popular'] ?? false),
                                'border border-violet-200 text-violet-700 hover:bg-violet-50' => ! ($plan['is_popular'] ?? false),
                            ])
                        >
                            {{ filled($plan['cta_label'] ?? null) ? $plan['cta_label'] : __('messages.consultation_cta') }}
                        </a>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
