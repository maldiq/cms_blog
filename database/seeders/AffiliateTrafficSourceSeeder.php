<?php

namespace Database\Seeders;

use App\Domain\Affiliate\Models\AffiliateTrafficSource;
use Illuminate\Database\Seeder;

class AffiliateTrafficSourceSeeder extends Seeder
{
    /**
     * Saran awal dari config. Seeder tidak menimpa baris yang sudah diubah
     * admin — hanya menambah kode yang belum ada.
     *
     * @var array<string, string>
     */
    private array $labels = [
        'fb_grup_gadget' => 'Facebook Grup Gadget',
        'fb_grup_promo' => 'Facebook Grup Promo',
        'fb_page' => 'Facebook Page',
        'fb_profile' => 'Facebook Profile',
        'fb_reels' => 'Facebook Reels',
        'ig_bio' => 'Instagram Bio',
        'ig_story' => 'Instagram Story',
        'tiktok_bio' => 'TikTok Bio',
        'newsletter' => 'Newsletter',
        'website' => 'Website',
    ];

    public function run(): void
    {
        $suggestions = (array) config('affiliate.tracking.source_suggestions', []);
        $codes = array_values(array_unique(array_merge(array_keys($this->labels), $suggestions)));
        $sort = 0;

        foreach ($codes as $code) {
            if (! is_string($code) || $code === '') {
                continue;
            }

            AffiliateTrafficSource::query()->firstOrCreate(
                ['code' => $code],
                [
                    'label' => $this->labels[$code] ?? null,
                    'is_active' => true,
                    'sort_order' => $sort,
                ],
            );

            $sort += 10;
        }
    }
}
