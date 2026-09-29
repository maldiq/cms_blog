@foreach ($items as $item)
    @php
        /** @var array<string, mixed> $item */
        $children = collect($item['children'] ?? []);
        $hasChildren = $children->isNotEmpty();
    @endphp
    <li @class(['relative', 'group' => $hasChildren])>
        <a
            href="{{ $item['url'] ?? '#' }}"
            target="{{ $item['target'] ?? '_self' }}"
            @if (($item['target'] ?? '_self') === '_blank') rel="noopener noreferrer" @endif
            class="inline-flex items-center gap-1 hover:text-emerald-700"
        >
            {{ $item['label'] ?? '' }}
            @if ($hasChildren)
                <span class="text-xs text-gray-400" aria-hidden="true">▾</span>
            @endif
        </a>

        @if ($hasChildren)
            <ul @class([
                'absolute left-0 top-full z-20 mt-1 min-w-[10rem] rounded-md border border-gray-200 bg-white py-2 shadow-lg',
                'hidden group-hover:block group-focus-within:block' => $depth === 0,
                'ml-4 mt-1 space-y-1 border-l border-gray-200 pl-3' => $depth > 0,
            ])>
                @include('components.partials.menu-items', ['items' => $children, 'depth' => $depth + 1])
            </ul>
        @endif
    </li>
@endforeach
