<div
    x-data="{
        visible: false,
        checkScroll() {
            this.visible = window.scrollY > 400;
        },
        scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
    }"
    x-init="checkScroll()"
    @scroll.window.passive="checkScroll()"
    class="pointer-events-none fixed inset-x-0 bottom-0 z-50 flex justify-end p-4 sm:p-6"
>
    <button
        type="button"
        x-show="visible"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-y-2 opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="translate-y-2 opacity-0"
        @click="scrollToTop()"
        :aria-hidden="! visible"
        class="pointer-events-auto inline-flex h-11 w-11 items-center justify-center rounded-full text-white shadow-lg transition hover:opacity-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900"
        style="background-color: var(--color-primary, #0f766e); --tw-ring-color: var(--color-primary, #0f766e);"
        aria-label="{{ __('messages.back_to_top') }}"
        title="{{ __('messages.back_to_top') }}"
    >
        <span class="sr-only">{{ __('messages.back_to_top') }}</span>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5" aria-hidden="true">
            <path fill-rule="evenodd" d="M10 17a.75.75 0 0 1-.75-.75V5.612L5.29 9.77a.75.75 0 0 1-1.06-1.06l5.25-5.5a.75.75 0 0 1 1.08 0l5.25 5.5a.75.75 0 1 1-1.06 1.06l-3.96-4.158V16.25A.75.75 0 0 1 10 17Z" clip-rule="evenodd" />
        </svg>
    </button>
</div>
