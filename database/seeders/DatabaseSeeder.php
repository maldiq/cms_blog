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
            LanguageSeeder::class,
            RolePermissionSeeder::class,
            SuperAdminSeeder::class,
            MenuSeeder::class,
            BlogSeeder::class,
            GallerySeeder::class,
            CommentSeeder::class,
            PageSeeder::class,
            ServiceProDemoSeeder::class,
            NewsletterSubscriberSeeder::class,
            ContactSubmissionSeeder::class,
        ]);
    }
}
