<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->word(rand(1, 3), true);

        return [
            'name' => $name,
//            'slug' => fake()->slug(),
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(),
            'parent_id' => null,
            'image' => fake()->imageUrl(),
            'is_active' => fake()->boolean(75),
            'order' => fake()->numberBetween(1, 100),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => fake()->dateTimeBetween('-1 year', 'now'),

        ];
    }
}
