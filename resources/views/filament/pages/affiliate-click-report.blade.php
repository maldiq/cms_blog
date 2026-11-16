<x-filament-panels::page>
    @php
        $summary = $this->summary;
        $sources = $this->bySource;
        $products = $this->byProduct;
        $locales = $this->byLocale;
        $labels = $this->sourceLabels;
        $sourceLabel = fn (?string $code): string => filled($code) ? ($labels[$code] ?? $code) : '-';
        $productTitle = fn ($click): string => (string) (
            $click->product?->translate('id', false)?->title
            ?? $click->product?->translate('en', false)?->title
            ?? '-'
        );
    @endphp

    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Ringkasan klik keluar per sumber traffic. Data ini mencatat klik, bukan komisi marketplace.
            </p>
        </div>

        <label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-200">
            <span>Periode</span>
            <select
                wire:model.live="period"
                class="rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-white/10 dark:bg-gray-900 dark:text-white"
            >
                @foreach ($this->getPeriodOptions() as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </label>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total klik</p>
            <p class="mt-2 text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">{{ number_format($summary['total'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sumber unik</p>
            <p class="mt-2 text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">{{ number_format($summary['sources'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sumber teratas</p>
            <p class="mt-2 truncate text-xl font-semibold tracking-tight text-gray-950 dark:text-white" title="{{ $sourceLabel($summary['top_source']) }}">{{ $sourceLabel($summary['top_source']) }}</p>
        </div>
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Produk teratas</p>
            <p class="mt-2 truncate text-xl font-semibold tracking-tight text-gray-950 dark:text-white" title="{{ $summary['top_product'] }}">{{ $summary['top_product'] ?: '-' }}</p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
            <div class="border-b border-gray-100 px-5 py-4 dark:border-white/10">
                <h2 class="text-base font-semibold text-gray-950 dark:text-white">Klik per sumber</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500 dark:bg-white/5 dark:text-gray-400">
                        <tr>
                            <th class="px-5 py-3 font-medium">Sumber</th>
                            <th class="px-5 py-3 text-right font-medium">Klik</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/10">
                        @forelse ($sources as $row)
                            <tr>
                                <td class="px-5 py-3">
                                    <span class="font-medium text-gray-950 dark:text-white">{{ $sourceLabel($row->source_label) }}</span>
                                    @if ($sourceLabel($row->source_label) !== $row->source_label)
                                        <span class="block font-mono text-xs text-gray-500 dark:text-gray-400">{{ $row->source_label }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-right text-gray-700 dark:text-gray-200">{{ number_format((int) $row->clicks, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-5 py-6 text-center text-gray-500 dark:text-gray-400">Belum ada klik pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
            <div class="border-b border-gray-100 px-5 py-4 dark:border-white/10">
                <h2 class="text-base font-semibold text-gray-950 dark:text-white">Klik per produk</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500 dark:bg-white/5 dark:text-gray-400">
                        <tr>
                            <th class="px-5 py-3 font-medium">Produk</th>
                            <th class="px-5 py-3 text-right font-medium">Klik</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/10">
                        @forelse ($products as $row)
                            <tr>
                                <td class="px-5 py-3 font-medium text-gray-950 dark:text-white">{{ $productTitle($row) }}</td>
                                <td class="px-5 py-3 text-right text-gray-700 dark:text-gray-200">{{ number_format((int) $row->clicks, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-5 py-6 text-center text-gray-500 dark:text-gray-400">Belum ada klik pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
        <div class="border-b border-gray-100 px-5 py-4 dark:border-white/10">
            <h2 class="text-base font-semibold text-gray-950 dark:text-white">Klik per locale</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500 dark:bg-white/5 dark:text-gray-400">
                    <tr>
                        <th class="px-5 py-3 font-medium">Locale</th>
                        <th class="px-5 py-3 text-right font-medium">Klik</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/10">
                    @forelse ($locales as $row)
                        <tr>
                            <td class="px-5 py-3 font-medium uppercase text-gray-950 dark:text-white">{{ $row->locale }}</td>
                            <td class="px-5 py-3 text-right text-gray-700 dark:text-gray-200">{{ number_format((int) $row->clicks, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-5 py-6 text-center text-gray-500 dark:text-gray-400">Belum ada klik pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
