<?php

namespace Database\Seeders;

use App\Domain\Affiliate\Models\AffiliateCategory;
use App\Domain\Language\Models\Language;
use Database\Seeders\Support\HostingEditorial;
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
        'Fashion',
        'Otomotif',
        'Buku',
        'VPS',
        'Shared Hosting',
        'Reseller Hosting',
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
        'Fashion' => 'Fashion',
        'Otomotif' => 'Automotive',
        'Buku' => 'Books',
        'VPS' => 'VPS',
        'Shared Hosting' => 'Shared Hosting',
        'Reseller Hosting' => 'Reseller Hosting',
        'Software' => 'Software',
        'Umum' => 'General',
    ];

    public function run(): void
    {
        $locales = Language::getActive()->pluck('code')->all();

        if ($locales === []) {
            $locales = [config('app.locale', 'id')];
        }

        $editorial = HostingEditorial::all();

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

            // Konten editorial hanya diisi bila kolomnya masih kosong, agar
            // suntingan admin tidak tertimpa saat seeder dijalankan ulang.
            $categoryEditorial = $editorial[$name] ?? null;

            if ($categoryEditorial === null) {
                continue;
            }

            foreach ($locales as $locale) {
                $translation = $category->translations()->where('locale', $locale)->first();
                $content = $categoryEditorial[$locale] ?? $categoryEditorial['id'];

                if ($translation === null) {
                    continue;
                }

                $dirty = false;

                foreach (['intro', 'meta_title', 'meta_description'] as $field) {
                    if (blank($translation->{$field}) && filled($content[$field] ?? null)) {
                        $translation->{$field} = $content[$field];
                        $dirty = true;
                    }
                }

                if (blank($translation->faq) && ! empty($content['faq'])) {
                    $translation->faq = $content['faq'];
                    $dirty = true;
                }

                if ($dirty) {
                    $translation->save();
                }
            }
        }
    }
}
