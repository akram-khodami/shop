<?php

namespace App\Services;

class BarcodeService
{
    public static function generateBarcode(): string
    {
        return fake()->ean13(); // تولید بارکد استاندارد EAN-13
    }
}
