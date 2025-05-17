<?php

namespace App\Repositories;

use App\Models\Brand;

class BrandRepository extends BaseRepository
{
    public function model(): string
    {
        return Brand::class;
    }

    public function getBrandProducts(Brand $brand)
    {
        return $brand->products()->paginate(10);
    }
}
