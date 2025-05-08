<?php

// database/factories/AttributeCategoryFactory.php

namespace Database\Factories;

use App\Models\Attribute;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttributeCategoryFactory extends Factory
{
    /**
     * نام مدل مرتبط با این فکتوری
     * (به null تنظیم کنید چون مدل نداریم)
     */
    protected $model = null;

    public function definition(): array
    {
        return [
            'attribute_id' => Attribute::inRandomOrder()->first()->id,
            'category_id' => Category::inRandomOrder()->first()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
