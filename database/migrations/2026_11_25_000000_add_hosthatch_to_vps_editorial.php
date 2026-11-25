<?php

use Database\Seeders\Support\HostingEditorial;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Sebutkan HostHatch di intro, meta description, dan FAQ kategori VPS.
     * Hanya konten yang masih memakai versi tanpa HostHatch yang diganti,
     * sehingga suntingan admin tidak tertimpa.
     */
    public function up(): void
    {
        $editorial = HostingEditorial::all()['VPS'];

        $signatures = [
            'id' => 'paket bulanan Hostinger, dan Droplet DigitalOcean',
            'en' => "Hostinger's monthly plans, and DigitalOcean Droplets",
        ];

        foreach ($signatures as $locale => $signature) {
            $content = $editorial[$locale] ?? null;

            if ($content === null) {
                continue;
            }

            $translation = DB::table('affiliate_category_translations')
                ->where('slug', 'vps')
                ->where('locale', $locale)
                ->first();

            if ($translation === null) {
                continue;
            }

            if (! str_contains((string) $translation->intro, $signature)) {
                continue;
            }

            DB::table('affiliate_category_translations')
                ->where('id', $translation->id)
                ->update([
                    'intro' => $content['intro'],
                    'meta_description' => $content['meta_description'],
                    'faq' => json_encode($content['faq'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        // Konten editorial sebelumnya tidak dikembalikan.
    }
};
