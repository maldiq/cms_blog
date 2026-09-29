<?php

namespace Database\Factories;

use App\Domain\Team\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Team>
 */
class TeamFactory extends Factory
{
    protected $model = Team::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'photo_id' => null,
            'email' => fake()->safeEmail(),
            'social_links' => [
                'linkedin' => fake()->url(),
            ],
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }
}
