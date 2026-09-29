<div class="space-y-6">
    <div class="flex flex-wrap gap-3">
        <input
            type="search"
            wire:model.live.debounce.300ms="search"
            placeholder="{{ __('messages.search') ?? 'Cari...' }}"
            class="rounded-lg border border-gray-300 px-3 py-2 text-sm"
        />
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        @foreach ($posts as $post)
            @include('blog.partials.post-card', ['post' => $post, 'locale' => $locale])
        @endforeach
    </div>

    <div>
        {{ $posts->links() }}
    </div>
</div>
