<section class="bg-gray-50 py-16 lg:py-20">
    <div class="mx-auto max-w-7xl px-4 lg:px-6">
        @if (filled($title) || filled($subtitle))
            <div class="mx-auto mb-10 max-w-3xl text-center">
                @if (filled($title))
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">{{ $title }}</h2>
                @endif
                @if (filled($subtitle))
                    <p class="mt-3 text-lg text-gray-600">{{ $subtitle }}</p>
                @endif
            </div>
        @endif

        @if ($posts->isNotEmpty())
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($posts as $post)
                    @include('theme::components.post-card', ['post' => $post])
                @endforeach
            </div>
        @endif

        <div class="mt-10 text-center">
            <a
                href="{{ route('blog.index', ['locale' => app()->getLocale()]) }}"
                class="inline-flex rounded-lg px-6 py-3 text-sm font-semibold text-white transition hover:opacity-90"
                style="background-color: var(--color-primary, currentColor)"
            >
                {{ __('messages.all_articles') }}
            </a>
        </div>
    </div>
</section>
