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

        @if ($testimonials->isNotEmpty())
            <div class="theme-testimonial-swiper swiper">
                <div class="swiper-wrapper">
                    @foreach ($testimonials as $testimonial)
                        <div class="swiper-slide h-auto">
                            @include('theme::components.testimonial-card', ['testimonial' => $testimonial])
                        </div>
                    @endforeach
                </div>
                <div class="swiper-pagination !static mt-8"></div>
            </div>
        @endif
    </div>
</section>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (typeof window.initThemeTestimonialSwiper !== 'function') {
                    return;
                }

                document.querySelectorAll('.theme-testimonial-swiper').forEach(function (element) {
                    window.initThemeTestimonialSwiper(element);
                });
            });
        </script>
    @endpush
@endonce
