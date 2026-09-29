@foreach ($items as $item)
    @php
        /** @var array<string, mixed> $item */
        $children = collect($item['children'] ?? []);
        $hasChildren = $children->isNotEmpty();
        $isSubmenu = $depth > 0;
    @endphp
    <li
        @class([
            'relative' => ! $isSubmenu && $hasChildren,
            'group/item' => ! $isSubmenu && $hasChildren,
            // Jembatan hover: li hanya setinggi link; tanpa ini kursor melewati celah mt-2 dan submenu hilang.
            'lg:before:absolute lg:before:inset-x-0 lg:before:top-full lg:before:block lg:before:h-3 lg:before:content-[""]' => ! $isSubmenu && $hasChildren,
        ])
    >
        <a
            href="{{ $item['url'] ?? '#' }}"
            target="{{ $item['target'] ?? '_self' }}"
            @if (($item['target'] ?? '_self') === '_blank') rel="noopener noreferrer" @endif
            @class([
                'inline-flex items-center gap-1.5 rounded-md px-1 py-2 transition-colors hover:text-[var(--color-primary,theme(colors.emerald.700))]' => ! $isSubmenu,
                'block w-full rounded-lg px-3 py-2.5 text-left text-sm font-medium leading-snug text-gray-600 transition-colors hover:bg-gray-50 hover:text-gray-900 focus-visible:bg-gray-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-primary,theme(colors.emerald.600))] focus-visible:ring-offset-1' => $isSubmenu,
            ])
        >
            {{ $item['label'] ?? '' }}
            @if ($hasChildren && ! $isSubmenu)
                <svg class="h-4 w-4 shrink-0 text-gray-400 transition group-hover/item:text-gray-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                </svg>
            @endif
        </a>

        @if ($hasChildren)
            <ul
                @class(
                    $depth === 0
                        ? [
                            'z-50 min-w-[15rem] max-w-[20rem] origin-top-left rounded-xl border border-gray-200/90 bg-white p-2 shadow-xl shadow-gray-300/30 ring-1 ring-black/5',
                            'max-lg:static max-lg:mt-2 max-lg:w-full max-lg:min-w-0 max-lg:max-w-none max-lg:translate-y-0 max-lg:opacity-100 max-lg:visible max-lg:bg-gray-50/80',
                            'lg:absolute lg:left-0 lg:top-full lg:mt-0 lg:pt-1 lg:invisible lg:translate-y-1 lg:opacity-0 lg:transition lg:duration-150 lg:ease-out',
                            'lg:group-hover/item:visible lg:group-hover/item:translate-y-0 lg:group-hover/item:opacity-100',
                            'lg:group-focus-within/item:visible lg:group-focus-within/item:translate-y-0 lg:group-focus-within/item:opacity-100',
                        ]
                        : ['mt-1 space-y-0.5 border-l border-gray-200 pl-3']
                )
                @if ($depth === 0)
                    role="menu"
                @endif
            >
                @include('components.partials.menu-items', ['items' => $children, 'depth' => $depth + 1])
            </ul>
        @endif
    </li>
@endforeach
