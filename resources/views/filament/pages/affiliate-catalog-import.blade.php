<x-filament-panels::page>
    <x-filament::section
        heading="Cara pakai"
        description="Isi link affiliate asli Anda, bukan link pencarian. Link akan langsung mengarah ke detail produk."
    >
        <div class="space-y-4 text-sm text-gray-700 dark:text-gray-200">
            <ol class="list-decimal space-y-2 pl-5">
                <li>Klik <strong>Unduh template CSV</strong> di kanan atas. Template sudah berisi slug semua produk.</li>
                <li>Buka di Excel/Google Sheets, isi kolom <code class="font-mono">affiliate_url</code> dengan link dari dashboard Shopee Affiliate / TikTok Shop.</li>
                <li>Isi <code class="font-mono">image_url</code> bila ingin gambar produk ikut diunduh. Boleh dikosongkan.</li>
                <li>Simpan sebagai CSV, unggah di bawah, lalu klik <strong>Impor</strong>.</li>
            </ol>

            <div class="overflow-hidden rounded-lg ring-1 ring-gray-950/5 dark:ring-white/10">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-gray-500 dark:bg-white/5 dark:text-gray-400">
                        <tr>
                            <th class="px-3 py-2 font-medium">slug</th>
                            <th class="px-3 py-2 font-medium">affiliate_url</th>
                            <th class="px-3 py-2 font-medium">image_url</th>
                            <th class="px-3 py-2 font-medium">price</th>
                            <th class="px-3 py-2 font-medium">original_price</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        <tr>
                            <td class="px-3 py-2 font-mono">wardah-cushion-series</td>
                            <td class="px-3 py-2 font-mono">https://s.shopee.co.id/abc123</td>
                            <td class="px-3 py-2 font-mono">https://.../gambar.jpg</td>
                            <td class="px-3 py-2 font-mono">129000</td>
                            <td class="px-3 py-2 font-mono">159000</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p class="text-xs text-gray-500 dark:text-gray-400">
                Kolom <code class="font-mono">price</code> dan <code class="font-mono">original_price</code> boleh dikosongkan.
                Pemisah koma maupun titik koma sama-sama didukung.
            </p>
        </div>
    </x-filament::section>

    <form wire:submit="import" class="space-y-6">
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
            {{ $this->form }}
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <x-filament::button type="submit" icon="heroicon-o-arrow-up-tray">
                Impor
            </x-filament::button>

            <p class="text-sm text-gray-500 dark:text-gray-400">
                Baris dengan slug tidak dikenal atau link tidak valid akan dilewati dan dilaporkan.
            </p>
        </div>
    </form>

    @if ($lastResult !== null)
        <x-filament::section heading="Hasil impor terakhir">
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-lg bg-gray-50 p-4 dark:bg-white/5">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Link diperbarui</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-950 dark:text-white">{{ $lastResult['updated'] }}</p>
                </div>
                <div class="rounded-lg bg-gray-50 p-4 dark:bg-white/5">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Gambar diunduh</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-950 dark:text-white">{{ $lastResult['images'] }}</p>
                </div>
                <div class="rounded-lg bg-gray-50 p-4 dark:bg-white/5">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Dilewati</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-950 dark:text-white">{{ $lastResult['skipped'] }}</p>
                </div>
            </div>

            @if ($lastResult['errors'] !== [])
                <ul class="mt-4 list-disc space-y-1 pl-5 text-sm text-amber-700 dark:text-amber-400">
                    @foreach (array_slice($lastResult['errors'], 0, 15) as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

                @if (count($lastResult['errors']) > 15)
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        ... dan {{ count($lastResult['errors']) - 15 }} masalah lain. Jalankan lewat CLI untuk daftar lengkap.
                    </p>
                @endif
            @endif
        </x-filament::section>
    @endif
</x-filament-panels::page>
