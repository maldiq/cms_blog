@if ($items->isNotEmpty())
    @php
        $item = $items->first();
    @endphp
    <a
        href="{{ $item['url'] ?? '#' }}"
        target="{{ $item['target'] ?? '_self' }}"
        @if (($item['target'] ?? '_self') === '_blank') rel="noopener noreferrer" @endif
        class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-semibold text-white transition hover:opacity-90"
        style="background-color: var(--color-primary)"
    >
        {{ $item['label'] ?? '' }}
    </a>
@endif
