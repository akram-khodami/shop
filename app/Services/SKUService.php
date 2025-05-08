<?php

namespace App\Services;

use Illuminate\Support\Str;

class SKUService
{
    public static function generateSKU(string $productName): string
    {
//        return fake()->regexify('[A-Z]{3}-[0-9]{4}');
        //در فروشگاه‌های بزرگ مثل آمازون یا دیجی‌کالا، SKU و barcode طبق قوانین خاصی ایجاد می‌شوند: 📌 SKU معمولاً شامل دسته‌بندی، برند، و شماره محصول است. 📌 barcode طبق استاندارد EAN-13 یا UPC تولید می‌شود و از پایگاه داده یا یک سیستم خارجی دریافت می‌شود.


        $prefix = strtoupper(Str::substr($productName, 0, 3)); // سه حرف اول نام محصول

        $randomNumber = mt_rand(100, 999); // عدد تصادفی

        return "{$prefix}-{$randomNumber}";
    }
}
