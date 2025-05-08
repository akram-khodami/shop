<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attribute>
 */
class AttributeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'is_required' => fake()->boolean(),
            'is_filterable' => fake()->boolean(),
            'is_public' => fake()->boolean(),
            'type' => fake()->randomElement(['text', 'number', 'select', 'checkbox', 'date']),
            'order' => fake()->numberBetween(1, 100),
            'unit' => fake()->word(),
        ];
    }
}
