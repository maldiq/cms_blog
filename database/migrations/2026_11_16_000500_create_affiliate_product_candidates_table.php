<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_product_candidates', function (Blueprint $table) {
            $table->id();
            $table->string('source_type', 40)->index();
            $table->string('source_name', 120)->nullable();
            $table->string('external_id', 120);
            $table->string('platform_slug', 40)->index();
            $table->string('title');
            $table->string('shop_name')->nullable();
            $table->string('image_url', 2048)->nullable();
            $table->string('product_url', 2048)->nullable();
            $table->string('affiliate_url', 2048)->nullable();
            $table->decimal('price', 14, 2)->nullable();
            $table->decimal('original_price', 14, 2)->nullable();
            $table->decimal('commission_rate', 5, 2)->nullable();
            $table->unsignedBigInteger('sold_count')->nullable();
            $table->decimal('rating', 3, 2)->nullable();
            $table->unsignedBigInteger('review_count')->nullable();
            $table->decimal('trend_score', 6, 2)->nullable();
            $table->text('trend_notes')->nullable();
            $table->string('category_guess', 120)->nullable();
            $table->json('raw_payload')->nullable();
            $table->string('status', 20)->default('new')->index();
            $table->foreignId('imported_product_id')->nullable()->constrained('affiliate_products')->nullOnDelete();
            $table->timestamp('fetched_at')->nullable();
            $table->timestamp('curated_at')->nullable();
            $table->timestamps();

            $table->unique(['platform_slug', 'external_id'], 'affiliate_candidate_external_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_product_candidates');
    }
};
