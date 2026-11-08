<?php

namespace Database\Seeders;

use App\Domain\Affiliate\Models\AffiliatePlatform;
use Illuminate\Database\Seeder;

class AffiliatePlatformSeeder extends Seeder
{
    /**
     * @var array<string, string>
     */
    private array $platforms = [
        'shopee' => 'Shopee',
        'tiktok_shop' => 'TikTok Shop',
        'amazon' => 'Amazon',
        'vps' => 'VPS / Hosting',
        'other' => 'Lainnya',
    ];

    public function run(): void
    {
        foreach ($this->platforms as $slug => $name) {
            AffiliatePlatform::query()->firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'is_active' => true,
                    'sort_order' => array_search($slug, array_keys($this->platforms), true),
                ],
            );
        }
    }
}
