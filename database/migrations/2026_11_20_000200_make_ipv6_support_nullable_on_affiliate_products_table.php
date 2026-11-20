<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('affiliate_products', function (Blueprint $table): void {
            // NULL berarti belum diketahui, bukan "tidak didukung".
            $table->boolean('ipv6_support')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('affiliate_products', function (Blueprint $table): void {
            $table->boolean('ipv6_support')->default(false)->nullable(false)->change();
        });
    }
};
