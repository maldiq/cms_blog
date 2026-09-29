<div class="w-full">
    @if (session('newsletter_subscribed'))
        <p class="rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
            {{ __('messages.newsletter_success') }}
        </p>
    @endif

    <form wire:submit="subscribe" class="flex flex-col gap-3 sm:flex-row">
        <label class="sr-only" for="newsletter-email">{{ $placeholder }}</label>
        <input
            id="newsletter-email"
            type="email"
            wire:model="email"
            @if (filled($placeholder)) placeholder="{{ $placeholder }}" @endif
            class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
            required
        />
        <button
            type="submit"
            class="shrink-0 rounded-lg px-6 py-3 text-sm font-semibold text-white transition hover:opacity-90"
            style="background-color: var(--color-primary, currentColor)"
        >
            {{ $buttonLabel ?? __('messages.newsletter_submit') }}
        </button>
    </form>

    @error('email')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
