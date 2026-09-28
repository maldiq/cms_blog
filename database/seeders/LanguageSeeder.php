<?php

namespace Database\Seeders;

use App\Domain\Language\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        Language::query()->updateOrCreate(
            ['code' => 'id'],
            [
                'name' => 'Indonesian',
                'native_name' => 'Indonesia',
                'flag' => '🇮🇩',
                'is_default' => true,
                'is_active' => true,
                'direction' => 'ltr',
                'sort_order' => 1,
            ]
        );

        Language::query()->updateOrCreate(
            ['code' => 'en'],
            [
                'name' => 'English',
                'native_name' => 'English',
                'flag' => '🇬🇧',
                'is_default' => false,
                'is_active' => true,
                'direction' => 'ltr',
                'sort_order' => 2,
            ]
        );
    }
}
