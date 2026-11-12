<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_banner_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_banner_id')->constrained('affiliate_banners')->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('badge', 60)->nullable();
            $table->string('title', 150)->nullable();
            $table->string('subtitle', 255)->nullable();
            $table->string('cta_label', 60)->nullable();
            $table->timestamps();

            $table->unique(['affiliate_banner_id', 'locale'], 'affiliate_banner_locale_unique');
        });

        $locales = $this->activeLocales();
        $now = now();

        DB::table('affiliate_banners')
            ->select(['id', 'badge', 'title', 'subtitle', 'cta_label'])
            ->orderBy('id')
            ->chunk(100, function ($banners) use ($locales, $now): void {
                $rows = [];

                foreach ($banners as $banner) {
                    foreach ($locales as $locale) {
                        $rows[] = [
                            'affiliate_banner_id' => $banner->id,
                            'locale' => $locale,
                            'badge' => $banner->badge,
                            'title' => $banner->title,
                            'subtitle' => $banner->subtitle,
                            'cta_label' => $banner->cta_label,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }

                if ($rows !== []) {
                    DB::table('affiliate_banner_translations')->insert($rows);
                }
            });

        Schema::table('affiliate_banners', function (Blueprint $table) {
            $table->dropColumn(['badge', 'title', 'subtitle', 'cta_label']);
        });
    }

    public function down(): void
    {
        Schema::table('affiliate_banners', function (Blueprint $table) {
            $table->string('badge', 60)->nullable()->after('id');
            $table->string('title', 150)->nullable()->after('badge');
            $table->string('subtitle', 255)->nullable()->after('title');
            $table->string('cta_label', 60)->nullable()->after('subtitle');
        });

        $fallbackLocale = $this->fallbackLocale();

        DB::table('affiliate_banners')
            ->select(['id'])
            ->orderBy('id')
            ->chunk(100, function ($banners) use ($fallbackLocale): void {
                foreach ($banners as $banner) {
                    $translation = DB::table('affiliate_banner_translations')
                        ->where('affiliate_banner_id', $banner->id)
                        ->orderByRaw('CASE WHEN locale = ? THEN 0 ELSE 1 END', [$fallbackLocale])
                        ->first();

                    if ($translation === null) {
                        continue;
                    }

                    DB::table('affiliate_banners')
                        ->where('id', $banner->id)
                        ->update([
                            'badge' => $translation->badge,
                            'title' => $translation->title,
                            'subtitle' => $translation->subtitle,
                            'cta_label' => $translation->cta_label,
                        ]);
                }
            });

        Schema::dropIfExists('affiliate_banner_translations');
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
