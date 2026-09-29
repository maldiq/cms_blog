@if ($items->isNotEmpty())
    <nav aria-label="{{ $location }} menu">
        <ul @class([
            'text-sm font-medium text-gray-700',
            'flex flex-col gap-1 lg:flex-row lg:flex-wrap lg:items-center lg:gap-6' => $location === 'header',
            'flex flex-wrap items-center gap-2' => $location !== 'header',
        ])>
            @include('components.partials.menu-items', ['items' => $items, 'depth' => 0])
        </ul>
    </nav>
@endif
