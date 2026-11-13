<div class="npmp">
@once
    <style>
        .npmp {
            --npmp-primary-600: rgba(var(--primary-600), 1);
            --npmp-primary-500: rgba(var(--primary-500), 1);
            --npmp-gray-950: rgba(var(--gray-950), 1);
            --npmp-gray-900: rgba(var(--gray-900), 1);
            --npmp-gray-800: rgba(var(--gray-800), 1);
            --npmp-gray-700: rgba(var(--gray-700), 1);
            --npmp-gray-600: rgba(var(--gray-600), 1);
            --npmp-gray-500: rgba(var(--gray-500), 1);
            --npmp-gray-400: rgba(var(--gray-400), 1);
            --npmp-gray-300: rgba(var(--gray-300), 1);
            --npmp-gray-200: rgba(var(--gray-200), 1);
            --npmp-gray-100: rgba(var(--gray-100), 1);
            --npmp-gray-50: rgba(var(--gray-50), 1);
            --npmp-danger-600: rgba(var(--danger-600), 1);
            --npmp-danger-500: rgba(var(--danger-500), 1);
        }

        .npmp-trigger-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 1rem;
        }

        .npmp-selected {
            display: flex;
            align-items: center;
            gap: .75rem;
            min-width: 0;
        }

        .npmp-selected img {
            width: 4rem;
            height: 4rem;
            flex-shrink: 0;
            border-radius: .5rem;
            border: 1px solid var(--npmp-gray-200);
            object-fit: cover;
        }

        .dark .npmp-selected img {
            border-color: var(--npmp-gray-700);
        }

        .npmp-selected-label {
            margin: 0;
            font-size: .875rem;
            font-weight: 500;
            color: var(--npmp-gray-950);
        }

        .dark .npmp-selected-label {
            color: #fff;
        }

        .npmp-selected-name {
            margin: 0;
            max-width: 20rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: .875rem;
            color: var(--npmp-gray-500);
        }

        .npmp-empty {
            margin: 0;
            font-size: .875rem;
            color: var(--npmp-gray-500);
        }

        .npmp-btn {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            border: 0;
            border-radius: .5rem;
            padding: .5rem 1rem;
            font-size: .875rem;
            font-weight: 600;
            cursor: pointer;
        }

        .npmp-btn-primary {
            background: var(--npmp-primary-600);
            color: #fff;
        }

        .npmp-btn-primary:hover {
            background: var(--npmp-primary-500);
        }

        .npmp-btn-primary:disabled {
            cursor: not-allowed;
            opacity: .5;
        }

        .npmp-btn-ghost {
            background: transparent;
            color: var(--npmp-gray-700);
        }

        .npmp-btn-ghost:hover {
            background: var(--npmp-gray-100);
        }

        .dark .npmp-btn-ghost {
            color: var(--npmp-gray-200);
        }

        .dark .npmp-btn-ghost:hover {
            background: var(--npmp-gray-800);
        }

        .npmp-btn-danger {
            border: 0;
            background: transparent;
            padding: 0;
            font-size: .875rem;
            font-weight: 500;
            color: var(--npmp-danger-600);
            cursor: pointer;
        }

        .npmp-btn-danger:hover {
            color: var(--npmp-danger-500);
        }

        .npmp-icon {
            width: 1.25rem;
            height: 1.25rem;
        }

        .npmp-overlay {
            position: fixed;
            inset: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: .5rem;
            background: rgba(3, 7, 18, .6);
        }

        @media (min-width: 640px) {
            .npmp-overlay {
                padding: 1rem;
            }
        }

        .npmp-dialog {
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 64rem;
            height: min(85dvh, 700px);
            max-height: calc(100dvh - 2rem);
            overflow: hidden;
            border-radius: .75rem;
            background: #fff;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, .3);
        }

        .dark .npmp-dialog {
            background: var(--npmp-gray-900);
        }

        .npmp-header {
            display: flex;
            flex-shrink: 0;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            padding: .75rem 1rem;
            border-bottom: 1px solid var(--npmp-gray-200);
        }

        .dark .npmp-header {
            border-color: var(--npmp-gray-700);
        }

        .npmp-title {
            margin: 0;
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--npmp-gray-950);
        }

        .dark .npmp-title {
            color: #fff;
        }

        .npmp-subtitle {
            margin: .25rem 0 0;
            font-size: .875rem;
            color: var(--npmp-gray-500);
        }

        .npmp-close {
            flex-shrink: 0;
            padding: .5rem;
            border: 0;
            border-radius: .5rem;
            background: transparent;
            color: var(--npmp-gray-500);
            line-height: 0;
            cursor: pointer;
        }

        .npmp-close:hover {
            background: var(--npmp-gray-100);
            color: var(--npmp-gray-700);
        }

        .dark .npmp-close:hover {
            background: var(--npmp-gray-800);
        }

        .npmp-tabs {
            display: flex;
            flex-shrink: 0;
            padding: 0 1.25rem;
            border-bottom: 1px solid var(--npmp-gray-200);
        }

        .dark .npmp-tabs {
            border-color: var(--npmp-gray-700);
        }

        .npmp-tab {
            border: 0;
            border-bottom: 2px solid transparent;
            background: transparent;
            padding: .75rem 1rem;
            font-size: .875rem;
            font-weight: 500;
            color: var(--npmp-gray-500);
            cursor: pointer;
        }

        .npmp-tab:hover {
            color: var(--npmp-gray-800);
        }

        .dark .npmp-tab:hover {
            color: var(--npmp-gray-200);
        }

        .npmp-tab.is-active {
            border-bottom-color: var(--npmp-primary-600);
            color: var(--npmp-primary-600);
        }

        .npmp-toolbar {
            display: flex;
            flex-shrink: 0;
            flex-wrap: wrap;
            align-items: center;
            gap: .75rem;
            padding: .75rem 1rem;
            border-bottom: 1px solid var(--npmp-gray-200);
        }

        .dark .npmp-toolbar {
            border-color: var(--npmp-gray-700);
        }

        .npmp-input,
        .npmp-select {
            height: 2.25rem;
            border: 1px solid var(--npmp-gray-300);
            border-radius: .5rem;
            background: #fff;
            padding: 0 .75rem;
            font-size: .875rem;
            color: var(--npmp-gray-950);
        }

        .npmp-input {
            flex: 1 1 14rem;
            min-width: 14rem;
        }

        .npmp-input:focus,
        .npmp-select:focus {
            outline: none;
            border-color: var(--npmp-primary-500);
            box-shadow: 0 0 0 1px var(--npmp-primary-500);
        }

        .dark .npmp-input,
        .dark .npmp-select {
            border-color: var(--npmp-gray-700);
            background: var(--npmp-gray-800);
            color: #fff;
        }

        .npmp-body {
            display: flex;
            flex: 1;
            flex-direction: column;
            min-height: 0;
            overflow: hidden;
        }

        @media (min-width: 768px) {
            .npmp-body {
                flex-direction: row;
            }
        }

        .npmp-main {
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            padding: 1rem;
        }

        .npmp-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .75rem;
        }

        @media (min-width: 640px) {
            .npmp-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        @media (min-width: 1024px) {
            .npmp-grid {
                grid-template-columns: repeat(5, minmax(0, 1fr));
            }
        }

        .npmp-thumb {
            position: relative;
            display: block;
            overflow: hidden;
            padding: 0;
            text-align: left;
            border: 2px solid transparent;
            border-radius: .5rem;
            background: var(--npmp-gray-100);
            cursor: pointer;
        }

        .dark .npmp-thumb {
            background: var(--npmp-gray-800);
        }

        .npmp-thumb:hover {
            border-color: var(--npmp-gray-300);
        }

        .dark .npmp-thumb:hover {
            border-color: var(--npmp-gray-600);
        }

        .npmp-thumb.is-selected {
            border-color: var(--npmp-primary-600);
            box-shadow: 0 0 0 2px var(--npmp-primary-500);
        }

        .npmp-thumb img {
            display: block;
            width: 100%;
            aspect-ratio: 1 / 1;
            object-fit: cover;
        }

        .npmp-thumb-check {
            position: absolute;
            top: .5rem;
            right: .5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: .25rem;
            border-radius: 9999px;
            background: var(--npmp-primary-600);
            color: #fff;
            box-shadow: 0 1px 2px rgba(0, 0, 0, .25);
        }

        .npmp-thumb-check .npmp-icon {
            width: 1rem;
            height: 1rem;
        }

        .npmp-thumb-name {
            display: block;
            overflow: hidden;
            padding: .375rem .5rem;
            font-size: .75rem;
            color: var(--npmp-gray-700);
            text-overflow: ellipsis;
            white-space: nowrap;
            background: rgba(255, 255, 255, .95);
        }

        .dark .npmp-thumb-name {
            background: rgba(17, 24, 39, .95);
            color: var(--npmp-gray-300);
        }

        .npmp-pagination {
            margin-top: 1rem;
        }

        .npmp-aside {
            flex-shrink: 0;
            max-height: 10rem;
            overflow-y: auto;
            padding: .75rem;
            border-top: 1px solid var(--npmp-gray-200);
            background: var(--npmp-gray-50);
        }

        @media (min-width: 768px) {
            .npmp-aside {
                width: 14rem;
                max-height: none;
                border-top: 0;
                border-left: 1px solid var(--npmp-gray-200);
            }
        }

        .dark .npmp-aside {
            border-color: var(--npmp-gray-700);
            background: var(--npmp-gray-950);
        }

        .npmp-aside img {
            width: 100%;
            height: 6rem;
            border-radius: .5rem;
            background: var(--npmp-gray-200);
            object-fit: contain;
        }

        @media (min-width: 768px) {
            .npmp-aside img {
                height: 10rem;
            }
        }

        .dark .npmp-aside img {
            background: var(--npmp-gray-800);
        }

        .npmp-aside-name {
            margin: .5rem 0 0;
            overflow-wrap: break-word;
            font-size: .875rem;
            font-weight: 600;
            color: var(--npmp-gray-950);
        }

        .dark .npmp-aside-name {
            color: #fff;
        }

        .npmp-dl {
            margin: .5rem 0 0;
        }

        .npmp-dl-row {
            display: flex;
            justify-content: space-between;
            gap: .75rem;
            margin-top: .25rem;
            font-size: .75rem;
        }

        .npmp-dl dt {
            color: var(--npmp-gray-500);
        }

        .npmp-dl dd {
            margin: 0;
            text-align: right;
            color: var(--npmp-gray-700);
        }

        .dark .npmp-dl dd {
            color: var(--npmp-gray-300);
        }

        .npmp-aside-empty {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            min-height: 10rem;
            text-align: center;
            font-size: .875rem;
            color: var(--npmp-gray-500);
        }

        .npmp-empty-state {
            padding: 2.5rem 1rem;
            text-align: center;
            border: 1px dashed var(--npmp-gray-300);
            border-radius: .75rem;
        }

        .dark .npmp-empty-state {
            border-color: var(--npmp-gray-700);
        }

        .npmp-empty-state .npmp-icon {
            width: 2.5rem;
            height: 2.5rem;
            margin: 0 auto;
            color: var(--npmp-gray-400);
        }

        .npmp-empty-title {
            margin: .75rem 0 0;
            font-size: .875rem;
            font-weight: 500;
            color: var(--npmp-gray-900);
        }

        .dark .npmp-empty-title {
            color: #fff;
        }

        .npmp-empty-text {
            margin: .25rem 0 0;
            font-size: .875rem;
            color: var(--npmp-gray-500);
        }

        .npmp-dropzone-wrap {
            min-height: 0;
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
        }

        .npmp-dropzone-inner {
            margin: 0 auto;
            max-width: 42rem;
        }

        .npmp-dropzone {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 16rem;
            padding: 0 1.5rem;
            text-align: center;
            border: 2px dashed var(--npmp-gray-300);
            border-radius: .75rem;
            background: var(--npmp-gray-50);
            cursor: pointer;
        }

        .npmp-dropzone:hover {
            border-color: var(--npmp-primary-500);
        }

        .dark .npmp-dropzone {
            border-color: var(--npmp-gray-700);
            background: var(--npmp-gray-950);
        }

        .npmp-dropzone .npmp-icon {
            width: 3rem;
            height: 3rem;
            color: var(--npmp-gray-400);
        }

        .npmp-dropzone-title {
            margin: .75rem 0 0;
            font-size: .875rem;
            font-weight: 600;
            color: var(--npmp-gray-900);
        }

        .dark .npmp-dropzone-title {
            color: #fff;
        }

        .npmp-dropzone-text {
            margin: .25rem 0 0;
            font-size: .875rem;
            color: var(--npmp-gray-500);
        }

        .npmp-upload-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-top: 1rem;
        }

        .npmp-upload-hint {
            font-size: .875rem;
            color: var(--npmp-gray-600);
        }

        .npmp-error {
            margin-top: .75rem;
            font-size: .875rem;
            color: var(--npmp-danger-600);
        }

        .npmp-upload-loading {
            margin-top: 1rem;
            font-size: .875rem;
            color: var(--npmp-gray-500);
        }

        .npmp-footer {
            display: flex;
            flex-shrink: 0;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            padding: .75rem 1rem;
            border-top: 1px solid var(--npmp-gray-200);
            background: #fff;
        }

        .dark .npmp-footer {
            border-color: var(--npmp-gray-700);
            background: var(--npmp-gray-900);
        }

        .npmp-footer-name {
            margin: 0;
            max-width: 60%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: .875rem;
            color: var(--npmp-gray-500);
        }

        .npmp-actions {
            display: flex;
            gap: .5rem;
        }
    </style>
@endonce

    @php
        $selectedMedia = $this->selectedMedia;
        $focusedMedia = $this->focusedMedia;
    @endphp

    <div class="npmp-trigger-row">
        @if ($selectedMedia)
            <div class="npmp-selected">
                <img
                    src="{{ $this->previewUrl($selectedMedia) }}"
                    alt="{{ $selectedMedia->name }}"
                >
                <div>
                    <p class="npmp-selected-label">Gambar terpilih</p>
                    <p class="npmp-selected-name">{{ $selectedMedia->name }}</p>
                </div>
                @if ($statePath !== null && ! $multiple)
                    <button
                        type="button"
                        wire:click="clearSelection"
                        class="npmp-btn-danger"
                    >
                        Hapus
                    </button>
                @endif
            </div>
        @else
            <p class="npmp-empty">Belum ada gambar yang dipilih.</p>
        @endif

        <button
            type="button"
            wire:click="openModal"
            class="npmp-btn npmp-btn-primary"
        >
            <x-filament::icon icon="heroicon-o-photo" class="npmp-icon" />
            {{ $selectedMedia ? 'Ganti gambar' : 'Pilih gambar' }}
        </button>
    </div>

    @if ($showModal)
        <div
            class="npmp-overlay"
            wire:keydown.escape="closeModal"
            wire:click.self="closeModal"
            role="dialog"
            aria-modal="true"
            aria-label="Pilih gambar"
        >
            <div class="npmp-dialog">
                <div class="npmp-header">
                    <div>
                        <h2 class="npmp-title">Pilih media</h2>
                        <p class="npmp-subtitle">Pustaka gambar dari media library.</p>
                    </div>
                    <button
                        type="button"
                        wire:click="closeModal"
                        class="npmp-close"
                        aria-label="Tutup pemilih media"
                    >
                        <x-filament::icon icon="heroicon-o-x-mark" class="npmp-icon" />
                    </button>
                </div>

                <div class="npmp-tabs">
                    <button
                        type="button"
                        wire:click="$set('activeTab', 'library')"
                        @class(['npmp-tab', 'is-active' => $activeTab === 'library'])
                    >
                        Pustaka media
                    </button>
                    @if (auth()->user()?->can('media.upload'))
                        <button
                            type="button"
                            wire:click="$set('activeTab', 'upload')"
                            @class(['npmp-tab', 'is-active' => $activeTab === 'upload'])
                        >
                            Unggah berkas
                        </button>
                    @endif
                </div>

                @if ($activeTab === 'library')
                    <div class="npmp-toolbar">
                        <input
                            type="search"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Cari nama gambar..."
                            aria-label="Cari gambar"
                            class="npmp-input"
                        >
                        <select
                            wire:model.live="collectionFilter"
                            aria-label="Filter koleksi"
                            class="npmp-select"
                        >
                            <option value="">Semua koleksi</option>
                            @foreach ($this->collections as $collection)
                                <option value="{{ $collection }}">{{ $collection }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="npmp-body">
                        <div class="npmp-main">
                            @if ($this->mediaItems->isEmpty())
                                <div class="npmp-empty-state">
                                    <x-filament::icon icon="heroicon-o-photo" class="npmp-icon" />
                                    <p class="npmp-empty-title">Tidak ada gambar ditemukan</p>
                                    <p class="npmp-empty-text">
                                        @if (filled($search) || filled($collectionFilter))
                                            Coba ubah pencarian atau filter koleksi.
                                        @else
                                            Belum ada gambar di pustaka media server ini.
                                        @endif
                                    </p>
                                    @if (auth()->user()?->can('media.upload'))
                                        <button
                                            type="button"
                                            wire:click="$set('activeTab', 'upload')"
                                            class="npmp-btn npmp-btn-primary"
                                            style="margin-top: 1rem;"
                                        >
                                            Unggah gambar
                                        </button>
                                    @else
                                        <p class="npmp-empty-text" style="margin-top: .75rem;">
                                            Minta administrator memberikan izin unggah media.
                                        </p>
                                    @endif
                                </div>
                            @else
                                <div class="npmp-grid">
                                    @foreach ($this->mediaItems as $media)
                                        @php
                                            $isSelected = in_array($media->id, $pendingMediaIds, true);
                                        @endphp
                                        <button
                                            type="button"
                                            wire:key="media-picker-media-{{ $media->id }}"
                                            wire:click="toggleSelection({{ $media->id }})"
                                            @class(['npmp-thumb', 'is-selected' => $isSelected])
                                            aria-label="Pilih {{ $media->name }}"
                                            aria-pressed="{{ $isSelected ? 'true' : 'false' }}"
                                        >
                                            <img
                                                src="{{ $this->previewUrl($media) }}"
                                                alt="{{ $media->name }}"
                                                loading="lazy"
                                            >
                                            @if ($isSelected)
                                                <span class="npmp-thumb-check">
                                                    <x-filament::icon icon="heroicon-o-check" class="npmp-icon" />
                                                </span>
                                            @endif
                                            <span class="npmp-thumb-name">{{ $media->name }}</span>
                                        </button>
                                    @endforeach
                                </div>

                                <div class="npmp-pagination">
                                    {{ $this->mediaItems->links() }}
                                </div>
                            @endif
                        </div>

                        <aside class="npmp-aside">
                            @if ($focusedMedia)
                                <img
                                    src="{{ $this->previewUrl($focusedMedia) }}"
                                    alt="{{ $focusedMedia->name }}"
                                >
                                <h3 class="npmp-aside-name">{{ $focusedMedia->name }}</h3>
                                <dl class="npmp-dl">
                                    <div class="npmp-dl-row">
                                        <dt>Jenis</dt>
                                        <dd>{{ $focusedMedia->mime_type }}</dd>
                                    </div>
                                    <div class="npmp-dl-row">
                                        <dt>Ukuran</dt>
                                        <dd>{{ $focusedMedia->human_readable_size }}</dd>
                                    </div>
                                    <div class="npmp-dl-row">
                                        <dt>Koleksi</dt>
                                        <dd>{{ $focusedMedia->collection_name }}</dd>
                                    </div>
                                </dl>
                            @else
                                <div class="npmp-aside-empty">
                                    Pilih gambar untuk melihat detail.
                                </div>
                            @endif
                        </aside>
                    </div>
                @elseif ($activeTab === 'upload')
                    <div class="npmp-dropzone-wrap">
                        <div class="npmp-dropzone-inner">
                            <label class="npmp-dropzone">
                                <x-filament::icon icon="heroicon-o-cloud-arrow-up" class="npmp-icon" />
                                <span class="npmp-dropzone-title">Pilih gambar untuk diunggah</span>
                                <span class="npmp-dropzone-text">PNG, JPG, GIF, atau WebP · Maksimal 10 MB per gambar</span>
                                <input
                                    type="file"
                                    wire:model="newUploads"
                                    accept="image/*"
                                    multiple
                                    style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0;"
                                >
                            </label>

                            @error('newUploads')
                                <p class="npmp-error">{{ $message }}</p>
                            @enderror
                            @error('newUploads.*')
                                <p class="npmp-error">{{ $message }}</p>
                            @enderror

                            @if ($newUploads !== [])
                                <div class="npmp-upload-row">
                                    <p class="npmp-upload-hint">{{ count($newUploads) }} gambar siap diunggah</p>
                                    <button
                                        type="button"
                                        wire:click="uploadNew"
                                        wire:loading.attr="disabled"
                                        wire:target="uploadNew,newUploads"
                                        class="npmp-btn npmp-btn-primary"
                                    >
                                        <span wire:loading.remove wire:target="uploadNew">Unggah gambar</span>
                                        <span wire:loading wire:target="uploadNew">Mengunggah...</span>
                                    </button>
                                </div>
                            @endif

                            <div wire:loading wire:target="newUploads" class="npmp-upload-loading">
                                Menyiapkan unggahan...
                            </div>
                        </div>
                    </div>
                @endif

                <div class="npmp-footer">
                    <p class="npmp-footer-name">
                        @if ($this->focusedMedia)
                            {{ $this->focusedMedia->name }}
                        @else
                            Belum ada media dipilih
                        @endif
                    </p>
                    <div class="npmp-actions">
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="npmp-btn npmp-btn-ghost"
                        >
                            Batal
                        </button>
                        <button
                            type="button"
                            wire:click="confirmSelection"
                            wire:loading.attr="disabled"
                            wire:target="confirmSelection"
                            @disabled($pendingMediaIds === [])
                            class="npmp-btn npmp-btn-primary"
                        >
                            {{ $multiple ? 'Pilih media' : 'Gunakan gambar' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
