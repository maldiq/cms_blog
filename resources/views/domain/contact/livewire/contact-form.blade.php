<div>
    @if (session('contact_sent'))
        <p class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
            {{ __('messages.contact_success') }}
        </p>
    @endif

    <form wire:submit="submit" class="space-y-4">
        <div>
            <label for="contact-name" class="mb-1 block text-sm font-medium text-gray-700">{{ __('messages.contact_name') }}</label>
            <input id="contact-name" type="text" wire:model="name" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm" required>
            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="contact-email" class="mb-1 block text-sm font-medium text-gray-700">{{ __('messages.contact_email') }}</label>
            <input id="contact-email" type="email" wire:model="email" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm" required>
            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="contact-subject" class="mb-1 block text-sm font-medium text-gray-700">{{ __('messages.contact_subject') }}</label>
            <input id="contact-subject" type="text" wire:model="subject" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm" required>
            @error('subject') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="contact-message" class="mb-1 block text-sm font-medium text-gray-700">{{ __('messages.contact_message') }}</label>
            <textarea id="contact-message" wire:model="message" rows="5" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm" required></textarea>
            @error('message') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <button
            type="submit"
            class="inline-flex rounded-lg px-6 py-3 text-sm font-semibold text-white transition hover:opacity-90"
            style="background-color: var(--color-primary, currentColor)"
        >
            {{ __('messages.contact_submit') }}
        </button>
    </form>
</div>
