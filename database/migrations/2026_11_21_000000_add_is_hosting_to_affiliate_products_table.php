<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('affiliate_products', function (Blueprint $table): void {
            $table->boolean('is_hosting')->default(false)->after('affiliate_platform_id');
        });

        // Tandai produk lama yang jelas berasal dari kategori/Platform VPS.
        $platformIds = DB::table('affiliate_platforms')->where('slug', 'vps')->pluck('id');
        $categoryIds = DB::table('affiliate_category_translations')
            ->where('slug', 'hosting-vps')
            ->pluck('affiliate_category_id');

        DB::table('affiliate_products')
            ->where(function ($query) use ($platformIds, $categoryIds): void {
                $query->whereIn('affiliate_platform_id', $platformIds)
                    ->orWhereIn('affiliate_category_id', $categoryIds);
            })
            ->update(['is_hosting' => true]);
    }

    public function down(): void
    {
        Schema::table('affiliate_products', function (Blueprint $table): void {
            $table->dropColumn('is_hosting');
        });
    }
};
