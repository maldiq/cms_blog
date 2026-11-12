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
        'vps' => 'VPS / Hosting',
        'other' => 'Lainnya',
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
                    'sort_order' => array_search($slug, array_keys($this->platforms), true),
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
