<?php

namespace Database\Factories;

use App\Domain\Newsletter\Models\NewsletterSubscriber;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NewsletterSubscriber>
 */
class NewsletterSubscriberFactory extends Factory
{
    protected $model = NewsletterSubscriber::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'locale' => fake()->randomElement(['id', 'en']),
            'is_active' => true,
            'subscribed_at' => now(),
            'unsubscribed_at' => null,
            'ip_address' => fake()->ipv4(),
        ];
    }

    public function unsubscribed(): static
    {
        return $this->state(fn (): array => [
            'is_active' => false,
            'unsubscribed_at' => now(),
        ]);
    }
}
