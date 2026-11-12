<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_category_id')->constrained('affiliate_categories')->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('name', 100);
            $table->string('slug', 120);
            $table->timestamps();

            $table->unique(['affiliate_category_id', 'locale'], 'affiliate_category_locale_unique');
            $table->unique(['slug', 'locale'], 'affiliate_category_slug_locale_unique');
        });

        $locales = $this->activeLocales();
        $now = now();

        DB::table('affiliate_categories')
            ->select(['id', 'name', 'slug'])
            ->orderBy('id')
            ->chunk(100, function ($categories) use ($locales, $now): void {
                $rows = [];

                foreach ($categories as $category) {
                    foreach ($locales as $locale) {
                        $rows[] = [
                            'affiliate_category_id' => $category->id,
                            'locale' => $locale,
                            'name' => $category->name,
                            'slug' => $category->slug,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }

                if ($rows !== []) {
                    DB::table('affiliate_category_translations')->insert($rows);
                }
            });

        Schema::table('affiliate_categories', function (Blueprint $table) {
            $table->dropColumn(['name', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::table('affiliate_categories', function (Blueprint $table) {
            $table->string('name', 100)->nullable()->after('id');
            $table->string('slug', 120)->nullable()->after('name');
        });

        $fallbackLocale = $this->fallbackLocale();

        DB::table('affiliate_categories')
            ->select(['id'])
            ->orderBy('id')
            ->chunk(100, function ($categories) use ($fallbackLocale): void {
                foreach ($categories as $category) {
                    $translation = DB::table('affiliate_category_translations')
                        ->where('affiliate_category_id', $category->id)
                        ->orderByRaw('CASE WHEN locale = ? THEN 0 ELSE 1 END', [$fallbackLocale])
                        ->first();

                    if ($translation === null) {
                        continue;
                    }

                    DB::table('affiliate_categories')
                        ->where('id', $category->id)
                        ->update(['name' => $translation->name, 'slug' => $translation->slug]);
                }
            });

        Schema::dropIfExists('affiliate_category_translations');
    }

    /**
     * @return list<string>
     */
    private function activeLocales(): array
    {
        $locales = Schema::hasTable('languages')
            ? DB::table('languages')->where('is_active', true)->orderBy('sort_order')->pluck('code')->all()
            : [];

        return $locales !== [] ? $locales : [config('app.locale', 'id')];
    }

    private function fallbackLocale(): string
    {
        $locale = Schema::hasTable('languages')
            ? DB::table('languages')->where('is_default', true)->value('code')
            : null;

        return (string) ($locale ?? config('app.locale', 'id'));
    }
};
