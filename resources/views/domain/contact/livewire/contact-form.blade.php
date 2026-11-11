<div>
    @if ($submitted)
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 text-sm text-emerald-800" role="status">
            {{ __('messages.contact_success') }}
        </div>
    @endif

    @if ($errorMessage)
        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3.5 text-sm text-red-800" role="alert">
            {{ $errorMessage }}
        </div>
    @endif

    <form wire:submit="submit" class="space-y-4">
        <div class="absolute -left-[9999px] h-0 w-0 overflow-hidden" aria-hidden="true">
            <label for="contact-website">Website</label>
            <input type="text" id="contact-website" wire:model="website" tabindex="-1" autocomplete="off" />
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="contact-name" class="mb-1.5 block text-sm font-medium text-slate-700">{{ __('messages.contact_name') }}</label>
                <input id="contact-name" type="text" wire:model="name" required
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-brand-300 focus:bg-white focus:ring-4 focus:ring-brand-100">
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="contact-email" class="mb-1.5 block text-sm font-medium text-slate-700">{{ __('messages.contact_email') }}</label>
                <input id="contact-email" type="email" wire:model="email" required
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-brand-300 focus:bg-white focus:ring-4 focus:ring-brand-100">
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="contact-phone" class="mb-1.5 block text-sm font-medium text-slate-700">{{ __('messages.contact_phone') }}</label>
                <input id="contact-phone" type="text" wire:model="phone"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-brand-300 focus:bg-white focus:ring-4 focus:ring-brand-100">
                @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="contact-subject" class="mb-1.5 block text-sm font-medium text-slate-700">{{ __('messages.contact_subject') }}</label>
                <input id="contact-subject" type="text" wire:model="subject" required
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-brand-300 focus:bg-white focus:ring-4 focus:ring-brand-100">
                @error('subject') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="contact-message" class="mb-1.5 block text-sm font-medium text-slate-700">{{ __('messages.contact_message') }}</label>
            <textarea id="contact-message" wire:model="message" rows="5" required
                      class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-brand-300 focus:bg-white focus:ring-4 focus:ring-brand-100"></textarea>
            @error('message') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <button
            type="submit"
            wire:loading.attr="disabled"
            class="inline-flex items-center justify-center rounded-full bg-brand-950 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-950/25 transition hover:bg-brand-900 disabled:cursor-not-allowed disabled:opacity-60"
        >
            <span wire:loading.remove wire:target="submit">{{ __('messages.contact_submit') }}</span>
            <span wire:loading wire:target="submit">{{ __('messages.contact_submitting') }}</span>
        </button>
    </form>
</div>
