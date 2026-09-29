<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_categories', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('portfolio_category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_category_id')->constrained('portfolio_categories')->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('name');
            $table->string('slug');
            $table->timestamps();

            $table->unique(['portfolio_category_id', 'locale'], 'pf_cat_trans_cat_locale_unique');
            $table->unique(['slug', 'locale'], 'pf_cat_trans_slug_locale_unique');
        });

        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cover_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('client_name')->nullable();
            $table->date('project_date')->nullable();
            $table->string('project_url')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('portfolio_categories')->nullOnDelete();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('portfolio_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained('portfolios')->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('title');
            $table->string('slug');
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->timestamps();

            $table->unique(['portfolio_id', 'locale']);
            $table->unique(['slug', 'locale']);
        });

        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('photo_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('email')->nullable();
            $table->json('social_links')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('team_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('name');
            $table->string('slug');
            $table->string('position')->nullable();
            $table->text('bio')->nullable();
            $table->timestamps();

            $table->unique(['team_id', 'locale']);
            $table->unique(['slug', 'locale']);
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('photo_id')->nullable()->constrained('media')->nullOnDelete();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('testimonial_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('testimonial_id')->constrained('testimonials')->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('author_name');
            $table->string('author_position')->nullable();
            $table->string('author_company')->nullable();
            $table->text('content')->nullable();
            $table->timestamps();

            $table->unique(['testimonial_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonial_translations');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('team_translations');
        Schema::dropIfExists('teams');
        Schema::dropIfExists('portfolio_translations');
        Schema::dropIfExists('portfolios');
        Schema::dropIfExists('portfolio_category_translations');
        Schema::dropIfExists('portfolio_categories');
    }
};
