<?php

use Database\Seeders\Support\HostingEditorial;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Perbarui intro, meta description, dan FAQ kategori hosting agar
     * menyebut seluruh penyedia yang sekarang ada di katalog. Hanya konten
     * lama yang masih memakai kalimat versi RackNerd-only yang diganti,
     * sehingga suntingan admin tidak tertimpa.
     */
    public function up(): void
    {
        $editorial = HostingEditorial::all();

        $targets = [
            'VPS' => [
                'slug' => 'vps',
                'signatures' => [
                    'id' => 'paket bulanan Hostinger, dan Droplet DigitalOcean',
                    'en' => 'NainPicks gathers virtual server plans',
                ],
            ],
            'Shared Hosting' => [
                'slug' => 'shared-hosting',
                'signatures' => [
                    'id' => 'paket RackNerd Black Friday dibedakan',
                    'en' => 'RackNerd Black Friday plans differ',
                ],
            ],
            'Reseller Hosting' => [
                'slug' => 'reseller-hosting',
                'signatures' => [
                    'id' => 'Paket RackNerd di kategori ini',
                    'en' => 'The RackNerd plans in this category',
                ],
            ],
        ];

        foreach ($targets as $name => $target) {
            foreach ($target['signatures'] as $locale => $signature) {
                $content = $editorial[$name][$locale] ?? null;

                if ($content === null) {
                    continue;
                }

                $translation = DB::table('affiliate_category_translations')
                    ->where('slug', $target['slug'])
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
    }

    public function down(): void
    {
        // Konten editorial lama tidak dikembalikan.
    }
};
