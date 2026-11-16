<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_clicks', function (Blueprint $table) {
            $table->id();
            $table->ulid('click_id')->unique();
            $table->foreignId('affiliate_product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('affiliate_destination_id')->nullable()->constrained()->nullOnDelete();
            $table->string('locale', 10)->index();
            $table->string('source', 64)->nullable()->index();
            $table->string('first_source', 64)->nullable();
            $table->string('referrer_host', 255)->nullable();
            $table->string('ip_hash', 64)->nullable();
            $table->string('user_agent_family', 40)->nullable();
            $table->timestamp('clicked_at')->index();
            $table->timestamps();

            $table->index(['affiliate_product_id', 'clicked_at'], 'affiliate_clicks_product_clicked_index');
            $table->index(['source', 'clicked_at'], 'affiliate_clicks_source_clicked_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_clicks');
    }
};
