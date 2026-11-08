<?php

namespace Database\Seeders;

use App\Domain\Affiliate\Models\AffiliateCategory;
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

    public function run(): void
    {
        foreach ($this->categories as $index => $name) {
            AffiliateCategory::query()->firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'is_active' => true,
                    'sort_order' => $index,
                ],
            );
        }
    }
}
