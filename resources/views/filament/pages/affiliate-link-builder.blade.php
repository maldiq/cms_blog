<x-filament-panels::page>
    <form wire:submit="generate" class="space-y-6">
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
            {{ $this->form }}
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <x-filament::button type="submit" icon="heroicon-o-link">
                Buat link
            </x-filament::button>

            <p class="text-sm text-gray-500 dark:text-gray-400">
                Tempel hasilnya di postingan Facebook, bio, atau newsletter.
            </p>
        </div>
    </form>

    @if (filled($data['generated_url'] ?? null))
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
            <div class="flex flex-col gap-3">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-200" for="generated-url">URL siap copy</label>
                <div class="flex flex-col gap-2 sm:flex-row">
                    <input
                        id="generated-url"
                        type="text"
                        readonly
                        value="{{ $data['generated_url'] }}"
                        class="w-full rounded-lg border-gray-300 bg-gray-50 font-mono text-sm text-gray-900 shadow-sm dark:border-white/10 dark:bg-gray-900 dark:text-white"
                        onclick="this.select()"
                    >
                    <x-filament::button
                        tag="a"
                        :href="$data['generated_url']"
                        target="_blank"
                        rel="noopener noreferrer"
                        color="gray"
                        icon="heroicon-o-arrow-top-right-on-square"
                    >
                        Buka
                    </x-filament::button>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Contoh link share: <code class="font-mono">https://domain-anda.com/id?src=fb_grup_gadget</code>
                </p>
            </div>
        </div>
    @endif
</x-filament-panels::page>
