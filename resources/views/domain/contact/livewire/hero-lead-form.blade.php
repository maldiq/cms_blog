<div>
    @if ($submitted)
        <p class="rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
            {{ __('messages.contact_success') }}
        </p>
    @else
        @if ($errorMessage)
            <p class="mb-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                {{ $errorMessage }}
            </p>
        @endif

        <form wire:submit="submit" class="space-y-4">
            <div class="absolute -left-[9999px] h-0 w-0 overflow-hidden" aria-hidden="true">
                <input type="text" wire:model="website" tabindex="-1" autocomplete="off" />
            </div>

            <div>
                <label for="hero-lead-name" class="mb-1 block text-sm font-medium text-gray-700">{{ __('messages.contact_name') }}</label>
                <input id="hero-lead-name" type="text" wire:model="name" class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-violet-500 focus:outline-none focus:ring-1 focus:ring-violet-500" required>
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="hero-lead-email" class="mb-1 block text-sm font-medium text-gray-700">{{ __('messages.contact_email') }}</label>
                <input id="hero-lead-email" type="email" wire:model="email" class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-violet-500 focus:outline-none focus:ring-1 focus:ring-violet-500" required>
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="hero-lead-message" class="mb-1 block text-sm font-medium text-gray-700">{{ __('messages.contact_message') }}</label>
                <textarea id="hero-lead-message" wire:model="message" rows="3" class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-violet-500 focus:outline-none focus:ring-1 focus:ring-violet-500" required></textarea>
                @error('message') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <button
                type="submit"
                wire:loading.attr="disabled"
                class="w-full rounded-lg px-6 py-3 text-sm font-semibold text-white transition hover:opacity-90 disabled:opacity-60"
                style="background-color: var(--color-primary, #7c3aed)"
            >
                <span wire:loading.remove wire:target="submit">{{ __('messages.hero_lead_submit') }}</span>
                <span wire:loading wire:target="submit">{{ __('messages.contact_submitting') }}</span>
            </button>
        </form>
    @endif
</div>
