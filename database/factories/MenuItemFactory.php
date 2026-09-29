<?php

namespace Database\Factories;

use App\Domain\Menu\Models\Menu;
use App\Domain\Menu\Models\MenuItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MenuItem>
 */
class MenuItemFactory extends Factory
{
    protected $model = MenuItem::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'menu_id' => Menu::factory(),
            'parent_id' => null,
            'type' => MenuItem::TYPE_LINK,
            'target_id' => null,
            'url' => fake()->url(),
            'label' => [
                'id' => fake()->words(2, true),
                'en' => fake()->words(2, true),
            ],
            'icon' => null,
            'target' => '_self',
            'roles' => null,
            'sort_order' => 0,
            'is_active' => true,
        ];
    }
}
