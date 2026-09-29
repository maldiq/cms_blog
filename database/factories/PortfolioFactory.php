<?php

namespace Database\Factories;

use App\Domain\Portfolio\Models\Portfolio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Portfolio>
 */
class PortfolioFactory extends Factory
{
    protected $model = Portfolio::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cover_id' => null,
            'client_name' => fake()->company(),
            'project_date' => fake()->date(),
            'project_url' => fake()->url(),
            'category_id' => null,
            'is_featured' => true,
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }
}
