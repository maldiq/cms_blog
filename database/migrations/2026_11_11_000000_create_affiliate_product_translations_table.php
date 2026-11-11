<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_product_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_product_id')->constrained('affiliate_products')->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['affiliate_product_id', 'locale'], 'affiliate_product_locale_unique');
            $table->unique(['slug', 'locale'], 'affiliate_product_slug_locale_unique');
        });

        $locales = Schema::hasTable('languages')
            ? DB::table('languages')->where('is_active', true)->orderBy('sort_order')->pluck('code')->all()
            : [];

        if ($locales === []) {
            $locales = [config('app.locale', 'id')];
        }

        $now = now();

        DB::table('affiliate_products')
            ->select(['id', 'title', 'slug', 'description'])
            ->orderBy('id')
            ->chunk(100, function ($products) use ($locales, $now): void {
                $rows = [];

                foreach ($products as $product) {
                    foreach ($locales as $locale) {
                        $rows[] = [
                            'affiliate_product_id' => $product->id,
                            'locale' => $locale,
                            'title' => $product->title,
                            'slug' => $product->slug,
                            'description' => $product->description,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }

                if ($rows !== []) {
                    DB::table('affiliate_product_translations')->insert($rows);
                }
            });

        Schema::table('affiliate_products', function (Blueprint $table) {
            $table->dropColumn(['title', 'slug', 'description']);
        });
    }

    public function down(): void
    {
        Schema::table('affiliate_products', function (Blueprint $table) {
            $table->string('title')->nullable()->after('image_id');
            $table->string('slug')->nullable()->after('title');
            $table->text('description')->nullable()->after('affiliate_url');
        });

        $fallbackLocale = Schema::hasTable('languages')
            ? DB::table('languages')->where('is_default', true)->value('code')
            : null;

        $fallbackLocale ??= config('app.locale', 'id');

        DB::table('affiliate_products')
            ->select(['id'])
            ->orderBy('id')
            ->chunk(100, function ($products) use ($fallbackLocale): void {
                foreach ($products as $product) {
                    $translation = DB::table('affiliate_product_translations')
                        ->where('affiliate_product_id', $product->id)
                        ->orderByRaw('CASE WHEN locale = ? THEN 0 ELSE 1 END', [$fallbackLocale])
                        ->first();

                    if ($translation === null) {
                        continue;
                    }

                    DB::table('affiliate_products')
                        ->where('id', $product->id)
                        ->update([
                            'title' => $translation->title,
                            'slug' => $translation->slug,
                            'description' => $translation->description,
                        ]);
                }
            });

        Schema::dropIfExists('affiliate_product_translations');
    }
};
