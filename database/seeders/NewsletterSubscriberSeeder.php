<?php

namespace Database\Seeders;

use App\Domain\Newsletter\Models\NewsletterSubscriber;
use Illuminate\Database\Seeder;

class NewsletterSubscriberSeeder extends Seeder
{
    public function run(): void
    {
        $demos = [
            [
                'email' => 'demo.subscriber@example.com',
                'locale' => 'id',
            ],
            [
                'email' => 'demo.en@example.com',
                'locale' => 'en',
            ],
        ];

        foreach ($demos as $row) {
            NewsletterSubscriber::query()->firstOrCreate(
                ['email' => $row['email']],
                [
                    'locale' => $row['locale'],
                    'is_active' => true,
                    'subscribed_at' => now()->subDays(3),
                    'unsubscribed_at' => null,
                    'ip_address' => '127.0.0.1',
                ],
            );
        }
    }
}
