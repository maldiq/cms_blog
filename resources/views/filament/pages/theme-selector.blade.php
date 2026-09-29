<x-filament-panels::page>
    <div class="mb-4 flex justify-end">
        <x-filament::button wire:click="refresh" color="gray" icon="heroicon-o-arrow-path">
            Refresh
        </x-filament::button>
    </div>

    @if ($themes === [])
        <div class="rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400">
            Belum ada theme terdaftar. Tambahkan folder dengan <code class="text-xs">theme.json</code> di
            <code class="text-xs">resources/views/themes/</code>.
        </div>
    @else
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($themes as $theme)
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <div class="aspect-video bg-gray-100 dark:bg-gray-800">
                        @if (! empty($theme['preview_url']))
                            <img src="{{ $theme['preview_url'] }}" alt="{{ $theme['name'] }}"
                                 class="h-full w-full object-cover" />
                        @else
                            <div class="flex h-full items-center justify-center text-sm text-gray-400">
                                No preview
                            </div>
                        @endif
                    </div>

                    <div class="space-y-3 p-5">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $theme['name'] }}
                                </h3>
                                @if (! empty($theme['author']))
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $theme['author'] }}</p>
                                @endif
                            </div>
                            @if ($theme['is_active'])
                                <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                    Active
                                </span>
                            @endif
                        </div>

                        <p class="text-xs text-gray-500 dark:text-gray-400">v{{ $theme['version'] }}</p>

                        @if (! empty($theme['description']))
                            <p class="line-clamp-2 text-sm text-gray-600 dark:text-gray-300">{{ $theme['description'] }}</p>
                        @endif

                        <div class="flex flex-wrap gap-2 pt-2">
                            @if (! $theme['is_active'])
                                <x-filament::button
                                    wire:click="activateTheme('{{ $theme['slug'] }}')"
                                    size="sm"
                                >
                                    Activate
                                </x-filament::button>
                            @else
                                <x-filament::button
                                    tag="a"
                                    href="{{ \App\Domain\Theme\Filament\Pages\ThemeEditor::getUrl() }}"
                                    size="sm"
                                    color="gray"
                                >
                                    Edit Settings
                                </x-filament::button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-filament-panels::page>
