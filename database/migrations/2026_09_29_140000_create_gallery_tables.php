<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('albums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cover_id')->nullable()->constrained('media')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('album_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('album_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['album_id', 'locale']);
            $table->unique(['slug', 'locale']);
        });

        Schema::create('album_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('album_id')->constrained()->cascadeOnDelete();
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            $table->string('caption')->nullable();
            $table->unsignedInteger('sort_order')->default(0);

            $table->unique(['album_id', 'media_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('album_media');
        Schema::dropIfExists('album_translations');
        Schema::dropIfExists('albums');
    }
};
