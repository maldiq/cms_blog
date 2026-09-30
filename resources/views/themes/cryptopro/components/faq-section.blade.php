<section class="bg-white py-16 lg:py-20">
    <div class="mx-auto max-w-3xl px-4 lg:px-6">
        @if (filled($title) || filled($subtitle))
            <div class="mb-10 text-center">
                @if (filled($title))
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">{{ $title }}</h2>
                @endif
                @if (filled($subtitle))
                    <p class="mt-3 text-lg text-gray-600">{{ $subtitle }}</p>
                @endif
            </div>
        @endif

        @if ($items->isNotEmpty())
            <div x-data="{ openIndex: 0 }" class="divide-y divide-gray-200 rounded-xl border border-gray-200">
                @foreach ($items as $index => $item)
                    <div class="p-4">
                        @if (filled($item['question']))
                            <button
                                type="button"
                                class="flex w-full items-center justify-between gap-4 text-left text-base font-semibold text-gray-900"
                                @click="openIndex = openIndex === {{ $index }} ? -1 : {{ $index }}"
                                :aria-expanded="(openIndex === {{ $index }}).toString()"
                            >
                                <span>{{ $item['question'] }}</span>
                                <span class="text-xl leading-none text-gray-500" aria-hidden="true">
                                    <span x-show="openIndex !== {{ $index }}">+</span>
                                    <span x-show="openIndex === {{ $index }}" x-cloak>−</span>
                                </span>
                            </button>
                        @endif

                        @if (filled($item['answer']))
                            <div
                                x-show="openIndex === {{ $index }}"
                                x-cloak
                                class="pt-3 text-sm leading-relaxed text-gray-600"
                            >
                                {{ $item['answer'] }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
