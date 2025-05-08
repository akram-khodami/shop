<?php
/// database/seeders/AttributeCategorySeeder.php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeCategorySeeder extends Seeder
{
    public function run()
    {
        // اطمینان از وجود داده‌های پایه
        if (Category::count() === 0) {
            Category::factory()->count(10)->create();
        }

        if (Attribute::count() === 0) {
            Attribute::factory()->count(20)->create();
        }

        // روش مستقیم با DB facade
        $data = [];

        $categories = Category::all();
        $attributes = Attribute::all();

        foreach ($categories as $category) {
            $randomAttributes = $attributes->random(rand(2, 5));
            foreach ($randomAttributes as $attribute) {
                $data[] = [
                    'attribute_id' => $attribute->id,
                    'category_id' => $category->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('attribute_category')->insert($data);
    }
}
