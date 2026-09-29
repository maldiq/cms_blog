<div>
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" wire:keydown.escape="closeModal">
            <div class="flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-xl bg-white shadow-xl dark:bg-gray-900">
                <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Pilih media</h2>
                    <button type="button" wire:click="closeModal" class="text-gray-500 hover:text-gray-700">&times;</button>
                </div>

                <div class="space-y-4 overflow-y-auto p-4">
                    <div class="flex flex-wrap gap-3">
                        <input
                            type="search"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Cari..."
                            class="rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800"
                        />
                        <select wire:model.live="collectionFilter" class="rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800">
                            <option value="">Semua koleksi</option>
                            @foreach (\App\Domain\Media\Models\Media::query()->select('collection_name')->distinct()->orderBy('collection_name')->pluck('collection_name') as $collection)
                                <option value="{{ $collection }}">{{ $collection }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="rounded-lg border border-dashed border-gray-300 p-4 dark:border-gray-600">
                        <label class="mb-2 block text-sm font-medium">Upload baru</label>
                        <input type="file" wire:model="newUploads" multiple accept="image/*" class="text-sm" />
                        @error('newUploads.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        <button
                            type="button"
                            wire:click="uploadNew"
                            wire:loading.attr="disabled"
                            class="mt-2 rounded-lg bg-emerald-600 px-3 py-1.5 text-sm text-white hover:bg-emerald-700"
                        >
                            Unggah
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
                        @foreach ($this->mediaItems as $media)
                            <button
                                type="button"
                                wire:click="toggleSelection({{ $media->id }})"
                                class="relative overflow-hidden rounded-lg ring-2 transition {{ in_array($media->id, $selectedMediaIds, true) ? 'ring-emerald-500' : 'ring-transparent' }}"
                            >
                                <img src="{{ $this->previewUrl($media) }}" alt="" class="aspect-square w-full object-cover" loading="lazy" />
                                @if (in_array($media->id, $selectedMediaIds, true))
                                    <span class="absolute right-1 top-1 rounded bg-emerald-600 px-1.5 text-xs text-white">✓</span>
                                @endif
                            </button>
                        @endforeach
                    </div>

                    <div>
                        {{ $this->mediaItems->links() }}
                    </div>
                </div>

                <div class="flex justify-end gap-2 border-t border-gray-200 px-4 py-3 dark:border-gray-700">
                    <button type="button" wire:click="closeModal" class="rounded-lg px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200">
                        Batal
                    </button>
                    <button
                        type="button"
                        wire:click="confirmSelection"
                        class="rounded-lg bg-emerald-600 px-4 py-2 text-sm text-white hover:bg-emerald-700"
                    >
                        Pilih ({{ count($selectedMediaIds) }})
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
