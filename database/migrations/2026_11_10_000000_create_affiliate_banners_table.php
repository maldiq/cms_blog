<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150)->nullable();
            $table->string('subtitle', 255)->nullable();
            $table->string('badge', 60)->nullable();
            $table->foreignId('image_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('link_url', 2048)->nullable();
            $table->string('cta_label', 60)->nullable();
            $table->string('theme', 20)->default('brand');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_banners');
    }
};
