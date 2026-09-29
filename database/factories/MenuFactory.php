<?php

namespace Database\Factories;

use App\Domain\Menu\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Menu>
 */
class MenuFactory extends Factory
{
    protected $model = Menu::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'location' => fake()->randomElement(['header', 'footer']),
            'is_active' => true,
        ];
    }
}
