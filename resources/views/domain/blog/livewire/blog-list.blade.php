<div>
    <div class="relative mx-auto w-full max-w-md">
        <label for="blog-search" class="sr-only">{{ __('messages.search') }}</label>
        <svg class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z" />
        </svg>
        <input
            id="blog-search"
            type="search"
            wire:model.live.debounce.300ms="search"
            placeholder="{{ __('messages.search') }}"
            class="h-11 w-full rounded-full border border-slate-200 bg-slate-50 pl-11 pr-4 text-sm outline-none transition placeholder:text-slate-400 focus:border-brand-300 focus:bg-white focus:ring-4 focus:ring-brand-100"
        />
    </div>

    @if ($posts->isEmpty())
        <div class="mt-8 rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center">
            <p class="text-sm text-slate-600">{{ __('messages.blog_empty') }}</p>
        </div>
    @else
        <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($posts as $post)
                @include('blog.partials.post-card', ['post' => $post, 'locale' => $locale])
            @endforeach
        </div>
    @endif

    @if ($posts->hasPages())
        <div class="mt-10">{{ $posts->links() }}</div>
    @endif
</div>
