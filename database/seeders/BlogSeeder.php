<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Nama lama untuk seeder artikel blog.
 *
 * Dipertahankan agar pemanggil lama tetap berjalan, tetapi isinya kini
 * artikel produk affiliate dan bukan lagi artikel contoh.
 */
class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(AffiliateBlogSeeder::class);
    }
}
