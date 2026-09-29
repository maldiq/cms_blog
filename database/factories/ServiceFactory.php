<?php

namespace Database\Factories;

use App\Domain\Service\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'icon' => '🛠️',
            'cover_id' => null,
            'price_from' => fake()->randomFloat(2, 100000, 5000000),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => ['is_active' => false]);
    }

    public function withTranslations(): static
    {
        return $this->afterCreating(function (Service $service): void {
            $service->translateOrNew('id')->fill([
                'title' => fake()->words(3, true),
                'slug' => fake()->unique()->slug(),
                'excerpt' => fake()->sentence(),
                'content' => '<p>' . fake()->paragraph() . '</p>',
            ])->save();

            $service->translateOrNew('en')->fill([
                'title' => fake()->words(3, true),
                'slug' => fake()->unique()->slug(),
                'excerpt' => fake()->sentence(),
                'content' => '<p>' . fake()->paragraph() . '</p>',
            ])->save();
        });
    }
}
