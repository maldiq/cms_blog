@if ($items->isNotEmpty())
    <nav aria-label="{{ $location }} menu">
        <ul class="flex flex-wrap items-center gap-4 text-sm font-medium text-gray-700">
            @include('components.partials.menu-items', ['items' => $items, 'depth' => 0])
        </ul>
    </nav>
@endif
