<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('affiliate_platforms', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 40)->unique();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $categoryIds = [];
        $usedSlugs = [];

        foreach (DB::table('affiliate_products')->select('category')->distinct()->orderBy('category')->pluck('category') as $categoryName) {
            $categoryName = trim((string) $categoryName);

            if ($categoryName === '') {
                continue;
            }

            $slug = Str::limit(Str::slug($categoryName), 120, '');
            $baseSlug = $slug !== '' ? $slug : 'kategori';
            $suffix = 2;

            while (isset($usedSlugs[$slug])) {
                $slug = Str::limit($baseSlug, 116, '').'-'.$suffix++;
            }

            $usedSlugs[$slug] = true;
            $categoryIds[$categoryName] = DB::table('affiliate_categories')->insertGetId([
                'name' => $categoryName,
                'slug' => $slug,
                'is_active' => true,
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $platformLabels = [
            'shopee' => 'Shopee',
            'tiktok_shop' => 'TikTok Shop',
            'amazon' => 'Amazon',
            'vps' => 'VPS / Hosting',
            'other' => 'Lainnya',
        ];
        $platformIds = [];
        $platformKeys = DB::table('affiliate_products')->select('platform')->distinct()->pluck('platform')
            ->map(fn ($platform): string => trim((string) $platform))
            ->filter()
            ->merge(array_keys($platformLabels))
            ->unique();

        foreach ($platformKeys as $platformKey) {
            $platformIds[$platformKey] = DB::table('affiliate_platforms')->insertGetId([
                'name' => $platformLabels[$platformKey] ?? Str::headline($platformKey),
                'slug' => Str::limit($platformKey, 40, ''),
                'is_active' => true,
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('affiliate_products', function (Blueprint $table) {
            $table->foreignId('affiliate_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('affiliate_platform_id')->nullable()->constrained()->nullOnDelete();
        });

        foreach (DB::table('affiliate_products')->get(['id', 'category', 'platform']) as $product) {
            DB::table('affiliate_products')
                ->where('id', $product->id)
                ->update([
                    'affiliate_category_id' => $categoryIds[trim((string) $product->category)] ?? null,
                    'affiliate_platform_id' => $platformIds[trim((string) $product->platform)] ?? null,
                ]);
        }

        Schema::table('affiliate_products', function (Blueprint $table) {
            $table->dropColumn(['category', 'platform']);
        });
    }

    public function down(): void
    {
        Schema::table('affiliate_products', function (Blueprint $table) {
            $table->string('category', 100)->nullable();
            $table->string('platform', 40)->nullable();
        });

        foreach (DB::table('affiliate_products')->get(['id', 'affiliate_category_id', 'affiliate_platform_id']) as $product) {
            DB::table('affiliate_products')
                ->where('id', $product->id)
                ->update([
                    'category' => DB::table('affiliate_categories')->where('id', $product->affiliate_category_id)->value('name'),
                    'platform' => DB::table('affiliate_platforms')->where('id', $product->affiliate_platform_id)->value('slug'),
                ]);
        }

        Schema::table('affiliate_products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('affiliate_category_id');
            $table->dropConstrainedForeignId('affiliate_platform_id');
        });

        Schema::dropIfExists('affiliate_platforms');
        Schema::dropIfExists('affiliate_categories');
    }
};
