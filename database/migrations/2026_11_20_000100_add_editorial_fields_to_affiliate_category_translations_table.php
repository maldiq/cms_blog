<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('affiliate_category_translations', function (Blueprint $table): void {
            $table->text('intro')->nullable()->after('slug');
            $table->string('meta_title', 255)->nullable()->after('intro');
            $table->string('meta_description', 320)->nullable()->after('meta_title');
            $table->json('faq')->nullable()->after('meta_description');
        });
    }

    public function down(): void
    {
        Schema::table('affiliate_category_translations', function (Blueprint $table): void {
            $table->dropColumn(['intro', 'meta_title', 'meta_description', 'faq']);
        });
    }
};
