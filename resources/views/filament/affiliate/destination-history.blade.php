@php
    $audits = $destination->audits()->with('user')->limit(50)->get();
@endphp

<div class="space-y-3">
    @forelse ($audits as $audit)
        <div class="rounded-xl border border-gray-200 p-4 text-sm dark:border-white/10">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <span class="font-semibold text-gray-950 dark:text-white">{{ $audit->actionLabel() }}</span>
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $audit->created_at?->format('d M Y H:i') }} · {{ $audit->user?->name ?? 'Sistem' }}
                </span>
            </div>

            @if (is_array($audit->changes) && $audit->changes !== [])
                <dl class="mt-3 space-y-2">
                    @foreach ($audit->changes as $field => $change)
                        <div class="grid gap-1 sm:grid-cols-[9rem_1fr]">
                            <dt class="font-medium text-gray-600 dark:text-gray-300">{{ $field }}</dt>
                            <dd class="break-all text-gray-800 dark:text-gray-100">
                                @if (is_array($change) && array_key_exists('old', $change))
                                    <span class="text-gray-500 line-through dark:text-gray-400">{{ $change['old'] === null || $change['old'] === '' ? '—' : $change['old'] }}</span>
                                    <span class="px-1 text-gray-400">→</span>
                                    <span class="font-medium">{{ $change['new'] === null || $change['new'] === '' ? '—' : $change['new'] }}</span>
                                @else
                                    {{ is_scalar($change) ? $change : json_encode($change) }}
                                @endif
                            </dd>
                        </div>
                    @endforeach
                </dl>
            @endif
        </div>
    @empty
        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada riwayat perubahan.</p>
    @endforelse
</div>
