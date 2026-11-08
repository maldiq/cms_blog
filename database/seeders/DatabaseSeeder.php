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
            AffiliateProductSeeder::class,
            PageSeeder::class,
            ServiceProThemeSeeder::class,
            ServiceProBlogSeeder::class,
            GallerySeeder::class,
            CommentSeeder::class,
            NewsletterSubscriberSeeder::class,
            ContactSubmissionSeeder::class,
        ]);
    }
}
