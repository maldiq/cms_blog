<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Nama lama untuk artikel blog bawaan theme ServicePro.
 *
 * Sejak katalog affiliate aktif, seeder ini meneruskan ke
 * {@see AffiliateBlogSeeder} agar tidak lagi membuat artikel dummy.
 */
class ServiceProBlogSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(AffiliateBlogSeeder::class);
    }
}
