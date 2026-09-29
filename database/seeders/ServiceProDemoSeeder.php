<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * @deprecated Gunakan ServiceProThemeSeeder.
 */
class ServiceProDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(ServiceProThemeSeeder::class);
    }
}
