<?php

namespace Database\Seeders;

use App\Domain\Affiliate\Models\AffiliatePlatform;
use App\Domain\Language\Models\Language;
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
        'racknerd' => 'RackNerd',
        'domainesia' => 'DomaiNesia',
        'vultr' => 'Vultr',
        'hostbrr' => 'HostBrr',
        'namecrane' => 'NameCrane',
        'hosthatch' => 'HostHatch',
        'vps' => 'VPS / Hosting',
        'other' => 'Lainnya',
    ];

    /**
     * Urutan tampilan platform di filter katalog.
     *
     * @var array<string, int>
     */
    private array $sortOrder = [
        'shopee' => 0,
        'tiktok_shop' => 1,
        'amazon' => 2,
        'racknerd' => 10,
        'domainesia' => 11,
        'vultr' => 12,
        'hostbrr' => 13,
        'namecrane' => 14,
        'hosthatch' => 15,
        'vps' => 20,
        'other' => 30,
    ];

    /**
     * @var array<string, string>
     */
    private array $englishNames = [
        'other' => 'Others',
    ];

    public function run(): void
    {
        $locales = Language::getActive()->pluck('code')->all();

        if ($locales === []) {
            $locales = [config('app.locale', 'id')];
        }

        foreach ($this->platforms as $slug => $name) {
            $platform = AffiliatePlatform::query()->firstOrCreate(
                ['slug' => $slug],
                [
                    'is_active' => true,
                    'sort_order' => $this->sortOrder[$slug] ?? 99,
                ],
            );

            foreach ($locales as $locale) {
                if ($platform->translations()->where('locale', $locale)->exists()) {
                    continue;
                }

                $localizedName = $locale === 'en'
                    ? ($this->englishNames[$slug] ?? $name)
                    : $name;

                $platform->translateOrNew($locale)->fill(['name' => $localizedName])->save();
            }
        }
    }
}
