<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_platform_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_platform_id')->constrained('affiliate_platforms')->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('name', 100);
            $table->timestamps();

            $table->unique(['affiliate_platform_id', 'locale'], 'affiliate_platform_locale_unique');
        });

        $locales = $this->activeLocales();
        $now = now();

        DB::table('affiliate_platforms')
            ->select(['id', 'name'])
            ->orderBy('id')
            ->chunk(100, function ($platforms) use ($locales, $now): void {
                $rows = [];

                foreach ($platforms as $platform) {
                    foreach ($locales as $locale) {
                        $rows[] = [
                            'affiliate_platform_id' => $platform->id,
                            'locale' => $locale,
                            'name' => $platform->name,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }

                if ($rows !== []) {
                    DB::table('affiliate_platform_translations')->insert($rows);
                }
            });

        Schema::table('affiliate_platforms', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }

    public function down(): void
    {
        Schema::table('affiliate_platforms', function (Blueprint $table) {
            $table->string('name', 100)->nullable()->after('id');
        });

        $fallbackLocale = $this->fallbackLocale();

        DB::table('affiliate_platforms')
            ->select(['id'])
            ->orderBy('id')
            ->chunk(100, function ($platforms) use ($fallbackLocale): void {
                foreach ($platforms as $platform) {
                    $translation = DB::table('affiliate_platform_translations')
                        ->where('affiliate_platform_id', $platform->id)
                        ->orderByRaw('CASE WHEN locale = ? THEN 0 ELSE 1 END', [$fallbackLocale])
                        ->first();

                    if ($translation === null) {
                        continue;
                    }

                    DB::table('affiliate_platforms')
                        ->where('id', $platform->id)
                        ->update(['name' => $translation->name]);
                }
            });

        Schema::dropIfExists('affiliate_platform_translations');
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
