<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DefaultContentSeeder extends Seeder
{
    /**
     * Konten dasar: bahasa, role, super-admin, dan pengaturan aplikasi.
     */
    public function run(): void
    {
        $this->call([
            LanguageSeeder::class,
            RolePermissionSeeder::class,
            SuperAdminSeeder::class,
        ]);

        $this->command->call('migrate', [
            '--path' => 'database/settings',
            '--force' => true,
        ]);

        $this->call(AppSettingsSeeder::class);
    }
}
