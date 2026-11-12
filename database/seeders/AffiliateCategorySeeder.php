<?php

namespace Database\Seeders;

use App\Domain\Affiliate\Models\AffiliateCategory;
use App\Domain\Language\Models\Language;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AffiliateCategorySeeder extends Seeder
{
    /**
     * @var list<string>
     */
    private array $categories = [
        'Elektronik',
        'Rumah Tangga',
        'Dapur',
        'Kecantikan',
        'Buku',
        'Hosting & VPS',
        'Software',
        'Umum',
    ];

    /**
     * @var array<string, string>
     */
    private array $englishNames = [
        'Elektronik' => 'Electronics',
        'Rumah Tangga' => 'Home & Living',
        'Dapur' => 'Kitchen',
        'Kecantikan' => 'Beauty',
        'Buku' => 'Books',
        'Hosting & VPS' => 'Hosting & VPS',
        'Software' => 'Software',
        'Umum' => 'General',
    ];

    public function run(): void
    {
        $locales = Language::getActive()->pluck('code')->all();

        if ($locales === []) {
            $locales = [config('app.locale', 'id')];
        }

        foreach ($this->categories as $index => $name) {
            $baseSlug = Str::slug($name);

            $category = AffiliateCategory::query()
                ->whereHas('translations', fn ($query) => $query->where('slug', $baseSlug))
                ->first();

            $category ??= AffiliateCategory::query()->create([
                'is_active' => true,
                'sort_order' => $index,
            ]);

            foreach ($locales as $locale) {
                if ($category->translations()->where('locale', $locale)->exists()) {
                    continue;
                }

                $localizedName = $locale === 'en'
                    ? ($this->englishNames[$name] ?? $name)
                    : $name;

                $category->translateOrNew($locale)->fill([
                    'name' => $localizedName,
                    'slug' => Str::slug($localizedName),
                ])->save();
            }
        }
    }
}
