<div class="space-y-4">
    @if ($submitted)
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            Komentar menunggu moderasi. Terima kasih!
        </div>
    @endif

    @if ($errorMessage)
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ $errorMessage }}
        </div>
    @endif

    <form wire:submit="submit" class="space-y-4">
        <div class="absolute -left-[9999px] h-0 w-0 overflow-hidden" aria-hidden="true">
            <label for="website">Website</label>
            <input type="text" id="website" wire:model="website" tabindex="-1" autocomplete="off" />
        </div>

        @guest
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700" for="authorName">Nama</label>
                    <input type="text" id="authorName" wire:model="authorName"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" />
                    @error('authorName') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700" for="authorEmail">Email</label>
                    <input type="email" id="authorEmail" wire:model="authorEmail"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" />
                    @error('authorEmail') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700" for="authorUrl">Website (opsional)</label>
                <input type="url" id="authorUrl" wire:model="authorUrl"
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" />
                @error('authorUrl') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        @endguest

        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700" for="content">Komentar</label>
            <textarea id="content" wire:model="content" rows="4"
                      class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>
            @error('content') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <button type="submit"
                class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
            Kirim komentar
        </button>
    </form>
</div>
