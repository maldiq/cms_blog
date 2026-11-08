<div>
    @php
        $selectedMedia = $this->selectedMedia;
        $focusedMedia = $this->focusedMedia;
    @endphp

    <div class="flex flex-wrap items-center gap-4">
        @if ($selectedMedia)
            <div class="flex min-w-0 items-center gap-3">
                <img
                    src="{{ $this->previewUrl($selectedMedia) }}"
                    alt="{{ $selectedMedia->name }}"
                    class="h-16 w-16 rounded-lg border border-gray-200 object-cover dark:border-gray-700"
                >
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-950 dark:text-white">Gambar terpilih</p>
                    <p class="max-w-xs truncate text-sm text-gray-500 dark:text-gray-400">{{ $selectedMedia->name }}</p>
                </div>
                @if ($statePath !== null && ! $multiple)
                    <button
                        type="button"
                        wire:click="clearSelection"
                        class="text-sm font-medium text-danger-600 hover:text-danger-500"
                    >
                        Hapus
                    </button>
                @endif
            </div>
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada gambar yang dipilih.</p>
        @endif

        <button
            type="button"
            wire:click="openModal"
            class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-500"
        >
            <x-filament::icon icon="heroicon-o-photo" class="h-5 w-5" />
            {{ $selectedMedia ? 'Ganti gambar' : 'Pilih gambar' }}
        </button>
    </div>

    @if ($showModal)
        <div
            class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-950/60 p-3 sm:p-6"
            wire:keydown.escape="closeModal"
            wire:click.self="closeModal"
            role="dialog"
            aria-modal="true"
            aria-label="Pilih gambar"
        >
            <div class="flex max-h-[92vh] w-full max-w-6xl flex-col overflow-hidden rounded-xl bg-white shadow-2xl dark:bg-gray-900">
                <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-950 dark:text-white">Pilih media</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pustaka gambar untuk produk affiliate.</p>
                    </div>
                    <button
                        type="button"
                        wire:click="closeModal"
                        class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-gray-800"
                        aria-label="Tutup pemilih media"
                    >
                        <x-filament::icon icon="heroicon-o-x-mark" class="h-5 w-5" />
                    </button>
                </div>

                <div class="flex border-b border-gray-200 px-5 dark:border-gray-700">
                    <button
                        type="button"
                        wire:click="$set('activeTab', 'library')"
                        @class([
                            'border-b-2 px-4 py-3 text-sm font-medium',
                            'border-primary-600 text-primary-600' => $activeTab === 'library',
                            'border-transparent text-gray-500 hover:text-gray-800 dark:hover:text-gray-200' => $activeTab !== 'library',
                        ])
                    >
                        Pustaka media
                    </button>
                    @if (auth()->user()?->can('media.upload'))
                        <button
                            type="button"
                            wire:click="$set('activeTab', 'upload')"
                            @class([
                                'border-b-2 px-4 py-3 text-sm font-medium',
                                'border-primary-600 text-primary-600' => $activeTab === 'upload',
                                'border-transparent text-gray-500 hover:text-gray-800 dark:hover:text-gray-200' => $activeTab !== 'upload',
                            ])
                        >
                            Unggah berkas
                        </button>
                    @endif
                </div>

                @if ($activeTab === 'library')
                    <div class="flex flex-wrap items-center gap-3 border-b border-gray-200 p-4 dark:border-gray-700">
                        <input
                            type="search"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Cari nama gambar..."
                            aria-label="Cari gambar"
                            class="min-w-56 flex-1 rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800"
                        >
                        <select
                            wire:model.live="collectionFilter"
                            aria-label="Filter koleksi"
                            class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800"
                        >
                            <option value="">Semua koleksi</option>
                            @foreach (\App\Domain\Media\Models\Media::query()->where('mime_type', 'like', 'image/%')->select('collection_name')->distinct()->orderBy('collection_name')->pluck('collection_name') as $collection)
                                <option value="{{ $collection }}">{{ $collection }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex min-h-0 flex-1 flex-col md:flex-row">
                        <div class="min-h-0 flex-1 overflow-y-auto p-4">
                            @if ($this->mediaItems->isEmpty())
                                <div class="rounded-xl border border-dashed border-gray-300 p-10 text-center dark:border-gray-700">
                                    <x-filament::icon icon="heroicon-o-photo" class="mx-auto h-10 w-10 text-gray-400" />
                                    <p class="mt-3 text-sm font-medium text-gray-900 dark:text-white">Tidak ada gambar ditemukan</p>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Coba ubah pencarian atau unggah gambar baru.</p>
                                </div>
                            @else
                                <div class="grid grid-cols-3 gap-3 sm:grid-cols-4 lg:grid-cols-5">
                                    @foreach ($this->mediaItems as $media)
                                        @php
                                            $isSelected = in_array($media->id, $pendingMediaIds, true);
                                        @endphp
                                        <button
                                            type="button"
                                            wire:key="affiliate-media-{{ $media->id }}"
                                            wire:click="toggleSelection({{ $media->id }})"
                                            @class([
                                                'group relative overflow-hidden rounded-lg border-2 bg-gray-100 text-left transition dark:bg-gray-800',
                                                'border-primary-600 ring-2 ring-primary-500' => $isSelected,
                                                'border-transparent hover:border-gray-300 dark:hover:border-gray-600' => ! $isSelected,
                                            ])
                                            aria-label="Pilih {{ $media->name }}"
                                            aria-pressed="{{ $isSelected ? 'true' : 'false' }}"
                                        >
                                            <img
                                                src="{{ $this->previewUrl($media) }}"
                                                alt="{{ $media->name }}"
                                                loading="lazy"
                                                class="aspect-square w-full object-cover"
                                            >
                                            @if ($isSelected)
                                                <span class="absolute right-2 top-2 rounded-full bg-primary-600 p-1 text-white shadow">
                                                    <x-filament::icon icon="heroicon-o-check" class="h-4 w-4" />
                                                </span>
                                            @endif
                                            <span class="block truncate bg-white/95 px-2 py-1.5 text-xs text-gray-700 dark:bg-gray-900/95 dark:text-gray-300">
                                                {{ $media->name }}
                                            </span>
                                        </button>
                                    @endforeach
                                </div>

                                <div class="mt-4">
                                    {{ $this->mediaItems->links() }}
                                </div>
                            @endif
                        </div>

                        <aside class="w-full border-t border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-950 md:w-64 md:border-l md:border-t-0">
                            @if ($focusedMedia)
                                <img
                                    src="{{ $this->previewUrl($focusedMedia) }}"
                                    alt="{{ $focusedMedia->name }}"
                                    class="aspect-square w-full rounded-lg bg-gray-200 object-contain dark:bg-gray-800"
                                >
                                <h3 class="mt-4 break-words text-sm font-semibold text-gray-900 dark:text-white">{{ $focusedMedia->name }}</h3>
                                <dl class="mt-3 space-y-2 text-xs">
                                    <div class="flex justify-between gap-3">
                                        <dt class="text-gray-500 dark:text-gray-400">Jenis</dt>
                                        <dd class="text-right text-gray-700 dark:text-gray-300">{{ $focusedMedia->mime_type }}</dd>
                                    </div>
                                    <div class="flex justify-between gap-3">
                                        <dt class="text-gray-500 dark:text-gray-400">Ukuran</dt>
                                        <dd class="text-right text-gray-700 dark:text-gray-300">{{ $focusedMedia->human_readable_size }}</dd>
                                    </div>
                                    <div class="flex justify-between gap-3">
                                        <dt class="text-gray-500 dark:text-gray-400">Koleksi</dt>
                                        <dd class="text-right text-gray-700 dark:text-gray-300">{{ $focusedMedia->collection_name }}</dd>
                                    </div>
                                </dl>
                            @else
                                <div class="flex h-full min-h-40 items-center justify-center text-center text-sm text-gray-500 dark:text-gray-400">
                                    Pilih gambar untuk melihat detail.
                                </div>
                            @endif
                        </aside>
                    </div>
                @elseif ($activeTab === 'upload')
                    <div class="min-h-0 flex-1 overflow-y-auto p-6">
                        <div class="mx-auto max-w-2xl">
                            <label class="flex min-h-64 cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 px-6 text-center transition hover:border-primary-500 hover:bg-primary-50/30 dark:border-gray-700 dark:bg-gray-950 dark:hover:border-primary-500">
                                <x-filament::icon icon="heroicon-o-cloud-arrow-up" class="h-12 w-12 text-gray-400" />
                                <span class="mt-4 text-sm font-semibold text-gray-900 dark:text-white">Pilih gambar untuk diunggah</span>
                                <span class="mt-1 text-sm text-gray-500 dark:text-gray-400">PNG, JPG, GIF, atau WebP · Maksimal 10 MB per gambar</span>
                                <input
                                    type="file"
                                    wire:model="newUploads"
                                    accept="image/*"
                                    multiple
                                    class="sr-only"
                                >
                            </label>

                            @error('newUploads')
                                <p class="mt-3 text-sm text-danger-600">{{ $message }}</p>
                            @enderror
                            @error('newUploads.*')
                                <p class="mt-3 text-sm text-danger-600">{{ $message }}</p>
                            @enderror

                            @if ($newUploads !== [])
                                <div class="mt-4 flex items-center justify-between gap-4">
                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ count($newUploads) }} gambar siap diunggah</p>
                                    <button
                                        type="button"
                                        wire:click="uploadNew"
                                        wire:loading.attr="disabled"
                                        wire:target="uploadNew,newUploads"
                                        class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-500 disabled:opacity-50"
                                    >
                                        <span wire:loading.remove wire:target="uploadNew">Unggah gambar</span>
                                        <span wire:loading wire:target="uploadNew">Mengunggah...</span>
                                    </button>
                                </div>
                            @endif

                            <div wire:loading wire:target="newUploads" class="mt-4 text-sm text-gray-500">
                                Menyiapkan unggahan...
                            </div>
                        </div>
                    </div>
                @endif

                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-gray-200 px-5 py-4 dark:border-gray-700">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        @if ($this->focusedMedia)
                            {{ $this->focusedMedia->name }}
                        @else
                            Belum ada media dipilih
                        @endif
                    </p>
                    <div class="flex gap-2">
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800"
                        >
                            Batal
                        </button>
                        <button
                            type="button"
                            wire:click="confirmSelection"
                            @disabled($pendingMediaIds === [])
                            class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-500 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            {{ $multiple ? 'Pilih media' : 'Gunakan gambar' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
