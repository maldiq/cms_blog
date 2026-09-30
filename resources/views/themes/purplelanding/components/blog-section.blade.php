<section id="blog" class="scroll-mt-24 bg-white py-16 lg:py-20">
    <div class="mx-auto max-w-7xl px-4 lg:px-6">
        @if (filled($title) || filled($subtitle))
            <div class="mx-auto mb-12 max-w-3xl text-center">
                <p class="text-sm font-semibold uppercase tracking-wider text-violet-600">{{ __('messages.purple_blog_eyebrow') }}</p>
                @if (filled($title))
                    <h2 class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">{{ $title }}</h2>
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
    </div>
</section>
