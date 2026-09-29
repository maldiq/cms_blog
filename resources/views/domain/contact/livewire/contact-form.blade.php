<div>
    @if ($submitted)
        <p class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
            {{ __('messages.contact_success') }}
        </p>
    @endif

    @if ($errorMessage)
        <p class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
            {{ $errorMessage }}
        </p>
    @endif

    <form wire:submit="submit" class="space-y-4">
        <div class="absolute -left-[9999px] h-0 w-0 overflow-hidden" aria-hidden="true">
            <label for="contact-website">Website</label>
            <input type="text" id="contact-website" wire:model="website" tabindex="-1" autocomplete="off" />
        </div>

        <div>
            <label for="contact-name" class="mb-1 block text-sm font-medium text-gray-700">{{ __('messages.contact_name') }}</label>
            <input id="contact-name" type="text" wire:model="name" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500" required>
            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="contact-email" class="mb-1 block text-sm font-medium text-gray-700">{{ __('messages.contact_email') }}</label>
            <input id="contact-email" type="email" wire:model="email" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500" required>
            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="contact-phone" class="mb-1 block text-sm font-medium text-gray-700">{{ __('messages.contact_phone') }}</label>
            <input id="contact-phone" type="text" wire:model="phone" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
            @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="contact-subject" class="mb-1 block text-sm font-medium text-gray-700">{{ __('messages.contact_subject') }}</label>
            <input id="contact-subject" type="text" wire:model="subject" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500" required>
            @error('subject') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="contact-message" class="mb-1 block text-sm font-medium text-gray-700">{{ __('messages.contact_message') }}</label>
            <textarea id="contact-message" wire:model="message" rows="5" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500" required></textarea>
            @error('message') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <button
            type="submit"
            wire:loading.attr="disabled"
            class="inline-flex rounded-lg px-6 py-3 text-sm font-semibold text-white transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-60"
            style="background-color: var(--color-primary, currentColor)"
        >
            <span wire:loading.remove wire:target="submit">{{ __('messages.contact_submit') }}</span>
            <span wire:loading wire:target="submit">{{ __('messages.contact_submitting') }}</span>
        </button>
    </form>
</div>
