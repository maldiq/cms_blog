<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('affiliate_products')
            ->whereNotNull('affiliate_url')
            ->where('affiliate_url', '!=', '')
            ->orderBy('id')
            ->chunkById(100, function ($products): void {
                foreach ($products as $product) {
                    $hasPrimary = DB::table('affiliate_destinations')
                        ->where('affiliate_product_id', $product->id)
                        ->where('is_primary', true)
                        ->exists();

                    if ($hasPrimary) {
                        continue;
                    }

                    DB::table('affiliate_destinations')->insert([
                        'affiliate_product_id' => $product->id,
                        'label' => 'Tujuan utama',
                        'url' => $product->affiliate_url,
                        'status' => 'active',
                        'is_primary' => true,
                        'priority' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });
    }

    public function down(): void
    {
        // Data hasil backfill tidak dihapus otomatis agar tidak menghilangkan
        // tujuan yang sudah diubah admin setelah migrasi berjalan.
    }
};
