@push('styles')
    <style>
        .theme-selector {
            display: flex;
            flex-direction: column;
            gap: 1.75rem;
        }

        .theme-selector__intro {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        @media (min-width: 640px) {
            .theme-selector__intro {
                flex-direction: row;
                align-items: flex-start;
                justify-content: space-between;
            }
        }

        .theme-selector__intro-text {
            max-width: 42rem;
            margin: 0;
            font-size: 0.875rem;
            line-height: 1.5;
            color: rgb(75 85 99);
        }

        .dark .theme-selector__intro-text {
            color: rgb(156 163 175);
        }

        .theme-selector__intro-meta {
            margin: 0.5rem 0 0;
            font-size: 0.75rem;
            color: rgb(107 114 128);
        }

        .theme-selector__grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.75rem;
        }

        @media (min-width: 1024px) {
            .theme-selector__grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (min-width: 1280px) {
            .theme-selector__grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        .theme-selector-card {
            position: relative;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border-radius: 1rem;
            border: 1px solid rgb(229 231 235);
            background: #fff;
            box-shadow: 0 1px 2px rgb(0 0 0 / 0.05);
        }

        .dark .theme-selector-card {
            border-color: rgb(55 65 81);
            background: rgb(17 24 39);
        }

        .theme-selector-card--active {
            border-color: rgb(5 150 105);
            box-shadow: 0 0 0 2px rgb(16 185 129 / 0.25);
        }

        .theme-selector-card__badge {
            position: absolute;
            z-index: 2;
            top: 1.25rem;
            right: 1.25rem;
        }

        .theme-selector-card__preview-wrap {
            padding: 1.25rem 1.25rem 0;
        }

        .theme-selector-card__preview {
            position: relative;
            overflow: hidden;
            border-radius: 0.75rem;
            aspect-ratio: 16 / 10;
            background: linear-gradient(135deg, rgb(17 94 89), rgb(19 78 74));
        }

        .theme-selector-card__preview img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .theme-selector-card__preview-placeholder {
            display: flex;
            height: 100%;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 1.5rem;
            text-align: center;
            color: rgb(255 255 255 / 0.8);
            font-size: 0.875rem;
        }

        .theme-selector-card__body {
            display: flex;
            flex: 1;
            flex-direction: column;
            gap: 1.25rem;
            padding: 1.25rem 1.5rem 1.5rem;
        }

        .theme-selector-card__title {
            margin: 0;
            font-size: 1.125rem;
            font-weight: 600;
            line-height: 1.3;
            color: rgb(3 7 18);
        }

        .dark .theme-selector-card__title {
            color: #fff;
        }

        .theme-selector-card__description {
            margin: 0.35rem 0 0;
            font-size: 0.875rem;
            line-height: 1.5;
            color: rgb(75 85 99);
        }

        .dark .theme-selector-card__description {
            color: rgb(156 163 175);
        }

        .theme-selector-card__meta {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.5rem;
            margin: 0;
            font-size: 0.75rem;
            color: rgb(107 114 128);
        }

        .theme-selector-card__meta-item {
            display: flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.5rem 0.625rem;
            border-radius: 0.5rem;
            background: rgb(249 250 251);
        }

        .dark .theme-selector-card__meta-item {
            background: rgb(31 41 55 / 0.8);
        }

        .theme-selector-card__meta-item--wide {
            grid-column: 1 / -1;
        }

        .theme-selector-card__actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: auto;
            padding-top: 1rem;
            border-top: 1px solid rgb(243 244 246);
        }

        .dark .theme-selector-card__actions {
            border-top-color: rgb(31 41 55);
        }
    </style>
@endpush

<x-filament-panels::page>
    @php
        $activeTheme = collect($themes)->firstWhere('is_active', true);
    @endphp

    <div class="theme-selector">
        <div class="theme-selector__intro">
            <div>
                <p class="theme-selector__intro-text">
                    Pilih dan kelola tampilan front-end. Theme aktif menentukan layout homepage, halaman layanan, dan pengaturan section di
                    <strong>Theme Editor</strong>.
                </p>
                @if ($activeTheme)
                    <p class="theme-selector__intro-meta">
                        Aktif saat ini: <strong>{{ $activeTheme['name'] }}</strong> · v{{ $activeTheme['version'] }}
                    </p>
                @endif
            </div>

            <x-filament::button wire:click="refresh" color="gray" icon="heroicon-o-arrow-path" size="sm">
                Muat ulang daftar
            </x-filament::button>
        </div>

        @if ($themes === [])
            <x-filament::section>
                <div class="flex flex-col items-center gap-3 py-10 text-center">
                    <x-filament::icon
                        icon="heroicon-o-swatch"
                        class="h-12 w-12 text-gray-400 dark:text-gray-500"
                    />
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Belum ada theme terdaftar</p>
                    <p class="max-w-md text-sm text-gray-500 dark:text-gray-400">
                        Buat folder di
                        <code class="rounded bg-gray-100 px-1.5 py-0.5 text-xs dark:bg-gray-800">resources/views/themes/{slug}/</code>
                        dengan file <code class="rounded bg-gray-100 px-1.5 py-0.5 text-xs dark:bg-gray-800">theme.json</code>.
                    </p>
                </div>
            </x-filament::section>
        @else
            <div class="theme-selector__grid">
                @foreach ($themes as $theme)
                    <article @class(['theme-selector-card', 'theme-selector-card--active' => $theme['is_active']])>
                        @if ($theme['is_active'])
                            <div class="theme-selector-card__badge">
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-primary-600 px-3 py-1 text-xs font-semibold text-white shadow-sm">
                                    <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
                                    Aktif
                                </span>
                            </div>
                        @endif

                        <div class="theme-selector-card__preview-wrap">
                            <div class="theme-selector-card__preview">
                                @if (! empty($theme['preview_url']))
                                    <img
                                        src="{{ $theme['preview_url'] }}"
                                        alt="Pratinjau {{ $theme['name'] }}"
                                        loading="lazy"
                                    />
                                @else
                                    <div class="theme-selector-card__preview-placeholder">
                                        <x-filament::icon icon="heroicon-o-photo" class="h-10 w-10 opacity-60" />
                                        <span>Pratinjau belum tersedia</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="theme-selector-card__body">
                            <div>
                                <h3 class="theme-selector-card__title">{{ $theme['name'] }}</h3>
                                @if (! empty($theme['description']))
                                    <p class="theme-selector-card__description">{{ $theme['description'] }}</p>
                                @endif
                            </div>

                            <dl class="theme-selector-card__meta">
                                <div class="theme-selector-card__meta-item">
                                    <x-filament::icon icon="heroicon-o-tag" class="h-4 w-4 shrink-0 opacity-70" />
                                    <span>v{{ $theme['version'] }}</span>
                                </div>
                                @if (! empty($theme['author']))
                                    <div class="theme-selector-card__meta-item">
                                        <x-filament::icon icon="heroicon-o-user-circle" class="h-4 w-4 shrink-0 opacity-70" />
                                        <span>{{ $theme['author'] }}</span>
                                    </div>
                                @endif
                                @if (($theme['settings_groups_count'] ?? 0) > 0)
                                    <div class="theme-selector-card__meta-item theme-selector-card__meta-item--wide">
                                        <x-filament::icon icon="heroicon-o-adjustments-horizontal" class="h-4 w-4 shrink-0 opacity-70" />
                                        <span>{{ $theme['settings_groups_count'] }} grup pengaturan section</span>
                                    </div>
                                @endif
                            </dl>

                            <div class="theme-selector-card__actions">
                                @if ($theme['is_active'])
                                    <x-filament::button
                                        tag="a"
                                        href="{{ \App\Domain\Theme\Filament\Pages\ThemeEditor::getUrl() }}"
                                        icon="heroicon-o-cog-6-tooth"
                                        size="sm"
                                    >
                                        Atur theme
                                    </x-filament::button>
                                    @if (! empty($theme['frontend_url']))
                                        <x-filament::button
                                            tag="a"
                                            href="{{ $theme['frontend_url'] }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            color="gray"
                                            icon="heroicon-o-arrow-top-right-on-square"
                                            size="sm"
                                        >
                                            Lihat situs
                                        </x-filament::button>
                                    @endif
                                @else
                                    <x-filament::button
                                        wire:click="activateTheme('{{ $theme['slug'] }}')"
                                        icon="heroicon-o-check-circle"
                                        size="sm"
                                    >
                                        Aktifkan
                                    </x-filament::button>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</x-filament-panels::page>
