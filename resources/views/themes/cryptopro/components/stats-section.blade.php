@php
    $sectionStyle = filled($primaryColor)
        ? "background-color: color-mix(in srgb, {$primaryColor} 12%, transparent);"
        : '';
@endphp

<section
    class="py-16"
    @if (filled($sectionStyle)) style="{{ $sectionStyle }}" @endif
    data-theme-stats
>
    <div class="mx-auto max-w-7xl px-4 lg:px-6">
        @if ($items->isNotEmpty())
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($items as $index => $item)
                    <div class="text-center">
                        <p class="text-3xl font-bold sm:text-4xl" style="color: var(--color-primary, {{ $primaryColor ?? 'inherit' }})">
                            <span
                                data-count-up
                                data-target="{{ $item['numeric_target'] }}"
                                data-prefix=""
                                data-suffix="{{ $item['suffix'] ?? '' }}"
                                data-display="{{ $item['number'] ?? '' }}"
                            >0</span>
                        </p>
                        @if (filled($item['label']))
                            <p class="mt-2 text-sm font-medium text-gray-700 sm:text-base">{{ $item['label'] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var counters = document.querySelectorAll('[data-count-up]');
                if (! counters.length) {
                    return;
                }

                var animateCounter = function (element) {
                    if (element.dataset.animated === '1') {
                        return;
                    }

                    element.dataset.animated = '1';

                    var target = parseFloat(element.dataset.target || '0');
                    var suffix = element.dataset.suffix || '';
                    var display = element.dataset.display || '';
                    var hasDecimal = display.indexOf('.') !== -1;
                    var duration = 1200;
                    var start = performance.now();

                    if (! target || Number.isNaN(target)) {
                        element.textContent = display;

                        return;
                    }

                    var step = function (now) {
                        var progress = Math.min((now - start) / duration, 1);
                        var value = target * progress;
                        element.textContent = (hasDecimal ? value.toFixed(1) : Math.round(value).toString()) + suffix;

                        if (progress < 1) {
                            requestAnimationFrame(step);
                        } else {
                            element.textContent = display || (String(target) + suffix);
                        }
                    };

                    requestAnimationFrame(step);
                };

                if (! ('IntersectionObserver' in window)) {
                    counters.forEach(animateCounter);

                    return;
                }

                var observer = new IntersectionObserver(function (entries, obs) {
                    entries.forEach(function (entry) {
                        if (! entry.isIntersecting) {
                            return;
                        }

                        animateCounter(entry.target);
                        obs.unobserve(entry.target);
                    });
                }, { threshold: 0.35 });

                counters.forEach(function (counter) {
                    observer.observe(counter);
                });
            });
        </script>
    @endpush
@endonce
