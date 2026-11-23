<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Rapikan urutan chip platform di filter katalog: marketplace lebih dulu,
     * lalu penyedia hosting, platform generik, dan terakhir "Lainnya".
     *
     * Seeder hanya mengisi sort_order saat baris pertama kali dibuat, sehingga
     * instalasi yang sudah ada perlu disamakan lewat migrasi ini.
     */
    public function up(): void
    {
        $order = [
            'shopee' => 0,
            'tiktok_shop' => 1,
            'amazon' => 2,
            'racknerd' => 10,
            'domainesia' => 11,
            'vultr' => 12,
            'hostbrr' => 13,
            'namecrane' => 14,
            'vps' => 20,
            'other' => 30,
        ];

        foreach ($order as $slug => $sortOrder) {
            DB::table('affiliate_platforms')
                ->where('slug', $slug)
                ->update([
                    'sort_order' => $sortOrder,
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        // Urutan platform sebelumnya tidak dikembalikan.
    }
};
