<?php

namespace Database\Seeders;

use App\Models\ShippingMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ShippingMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ShippingMethod::insert(
            [
                ['name' => ' ارسال پست-پست سفارشی', 'description' => Str::random(25), 'base_price' => 45000, 'is_active' => true, 'estimated_days' => 7, 'created_at' => now(), 'updated_at' => now()],//5-7days max:40kg
                ['name' => ' ارسال پست-پست پیشتار', 'description' => '', 'base_price' => 45000, 'is_active' => true, 'estimated_days' => 7, 'created_at' => now(), 'updated_at' => now()],//1-3days max:25kg
                ['name' => ' ارسال پست-پست ویژه', 'description' => '', 'base_price' => 45000, 'is_active' => true, 'estimated_days' => 7, 'created_at' => now(), 'updated_at' => now()],//0-1days max:
                ['name' => ' ارسال پست-رستا نت', 'description' => '', 'base_price' => 45000, 'is_active' => true, 'estimated_days' => 7, 'created_at' => now(), 'updated_at' => now()],//x days max:
                ['name' => ' ارسال پست-بین الملل', 'description' => '', 'base_price' => 45000, 'is_active' => true, 'estimated_days' => 7, 'created_at' => now(), 'updated_at' => now()],//x days max:
                ['name' => 'تیپاکس', 'description' => '', 'base_price' => 45000, 'is_active' => true, 'estimated_days' => 7, 'created_at' => now(), 'updated_at' => now()],//0-4days max:
                ['name' => 'ارسال با پیک', 'description' => '', 'base_price' => 45000, 'is_active' => true, 'estimated_days' => 7, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'ارسال با اتوبوش', 'description' => '', 'base_price' => 45000, 'is_active' => true, 'estimated_days' => 7, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'باربری', 'description' => '', 'base_price' => 45000, 'is_active' => true, 'estimated_days' => 7, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'حمل و نقل هوایی', 'description' => '', 'base_price' => 45000, 'is_active' => true, 'estimated_days' => 7, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'چائپار', 'description' => '', 'base_price' => 45000, 'is_active' => true, 'estimated_days' => 7, 'created_at' => now(), 'updated_at' => now()],//0-4days max:
            ]);
    }
}
