<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_destinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_product_id')->constrained()->cascadeOnDelete();
            $table->string('label', 120)->default('Tujuan utama');
            $table->string('url', 2048);
            $table->string('status', 20)->default('active')->index();
            $table->boolean('is_primary')->default(false);
            $table->unsignedInteger('priority')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->unsignedSmallInteger('last_check_status')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['affiliate_product_id', 'status', 'priority'], 'affiliate_destinations_product_status_priority_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_destinations');
    }
};
