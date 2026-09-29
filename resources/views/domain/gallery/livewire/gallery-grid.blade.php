<div>
    @if ($items->isEmpty())
        <p class="text-gray-600">{{ __('Album ini belum memiliki foto.') }}</p>
    @else
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:gap-4 lg:grid-cols-4">
            @foreach ($items as $item)
                <a href="{{ $item['full'] }}"
                   class="glightbox group block overflow-hidden rounded-lg bg-gray-100"
                   data-gallery="album-gallery"
                   data-title="{{ $item['caption'] ?: $item['name'] }}">
                    <img src="{{ $item['thumb'] }}"
                         alt="{{ $item['caption'] ?: $item['name'] }}"
                         class="aspect-square w-full object-cover transition group-hover:opacity-90"
                         loading="lazy" />
                </a>
            @endforeach
        </div>
    @endif
</div>

@assets
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />
@endassets

@script
    <script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>
    <script>
        const lightbox = GLightbox({
            selector: '.glightbox',
            touchNavigation: true,
            loop: true,
        });

        Livewire.hook('morph.updated', () => {
            lightbox.reload();
        });
    </script>
@endscript
