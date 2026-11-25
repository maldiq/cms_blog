<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DefaultContentSeeder::class,
            AffiliateCategorySeeder::class,
            AffiliatePlatformSeeder::class,
            AffiliateTrafficSourceSeeder::class,
            AffiliateProductSeeder::class,
            RackNerdProductSeeder::class,
            PartnerHostingProductSeeder::class,
            HostHatchProductSeeder::class,
            AffiliateBannerSeeder::class,
            PageSeeder::class,
            ServiceProThemeSeeder::class,
            AffiliateBlogSeeder::class,
            GallerySeeder::class,
            NewsletterSubscriberSeeder::class,
            ContactSubmissionSeeder::class,
        ]);
    }
}
