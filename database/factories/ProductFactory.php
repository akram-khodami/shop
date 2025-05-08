<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
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
            'slug' => fake()->slug(),
            'description' => fake()->text(200),
            'price' => fake()->randomFloat(2, 1000, 999999),
            'sale_price' => fake()->randomFloat(2, 900, 999990),
            'is_active' => fake()->boolean(),
            'is_featured' => fake()->boolean(),
            'sku' => fake()->unique()->numerify('SKU-####'),//یک مقدار عددی تصادفی با پیشوند SKU- تولید شود مثلاً SKU-2435
//            'sku' => fake()->regexify('[A-Z]{3}-[0-9]{4}'), // نمونه: ABC-2345
            'barcode' => fake()->ean13(),//یک بارکد استاندارد ۱۳ رقمی تولید می‌کند
            'weight' => fake()->randomFloat(2, 0.5, 10),
            'dimensions' => fake()->randomElement(['10x10x10', '20x15x5', '30x20x15']),
            'stock' => fake()->numberBetween(0, 100),//تعداد موجودی محصول
        ];
    }
}
