<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'icon' => 'fa-' . fake()->word(),
            'parent_category_id' => null,
        ];
    }

    /**
     * Indicate that the category has a parent.
     */
    public function withParent(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'parent_category_id' => Category::factory(),
            ];
        });
    }
}