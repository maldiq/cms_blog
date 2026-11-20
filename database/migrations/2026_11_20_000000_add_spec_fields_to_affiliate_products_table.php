<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('affiliate_products', function (Blueprint $table): void {
            $table->string('billing_period', 20)->nullable()->after('original_price');
            $table->unsignedSmallInteger('cpu_cores')->nullable()->after('billing_period');
            $table->unsignedInteger('ram_mb')->nullable()->after('cpu_cores');
            $table->unsignedInteger('storage_gb')->nullable()->after('ram_mb');
            $table->string('storage_type', 20)->nullable()->after('storage_gb');
            $table->decimal('bandwidth_tb', 8, 2)->nullable()->after('storage_type');
            $table->unsignedSmallInteger('ipv4_count')->nullable()->after('bandwidth_tb');
            $table->boolean('ipv6_support')->default(false)->after('ipv4_count');
            $table->string('port_speed', 20)->nullable()->after('ipv6_support');
            $table->string('location', 120)->nullable()->after('port_speed');
            $table->timestamp('last_checked_at')->nullable()->after('location');
        });
    }

    public function down(): void
    {
        Schema::table('affiliate_products', function (Blueprint $table): void {
            $table->dropColumn([
                'billing_period',
                'cpu_cores',
                'ram_mb',
                'storage_gb',
                'storage_type',
                'bandwidth_tb',
                'ipv4_count',
                'ipv6_support',
                'port_speed',
                'location',
                'last_checked_at',
            ]);
        });
    }
};
