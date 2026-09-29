<div class="w-full">
    @if ($subscribed)
        <p class="rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
            {{ __('messages.newsletter_success') }}
        </p>
    @endif

    @if ($errorMessage)
        <p class="mb-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
            {{ $errorMessage }}
        </p>
    @endif

    <form wire:submit="subscribe" class="flex flex-col gap-3 sm:flex-row">
        <label class="sr-only" for="newsletter-email">{{ $placeholder ?? __('messages.newsletter_submit') }}</label>
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
            wire:loading.attr="disabled"
            class="shrink-0 rounded-lg px-6 py-3 text-sm font-semibold text-white transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-60"
            style="background-color: var(--color-primary, currentColor)"
        >
            <span wire:loading.remove wire:target="subscribe">{{ $buttonLabel ?? __('messages.newsletter_submit') }}</span>
            <span wire:loading wire:target="subscribe">{{ __('messages.newsletter_submitting') }}</span>
        </button>
    </form>

    @error('email')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
