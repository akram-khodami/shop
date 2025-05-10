<?php

namespace App\Repositories;

use App\Models\Brand;

class BrandRepository
{
    public function all()
    {
        return Brand::paginate(10);
    }

    public function create(array $data)
    {
        return Brand::create($data);
    }

    public function update(Brand $brand, array $data)
    {
        return $brand->update($data);
    }

    public function delete(Brand $brand)
    {
        return $brand->delete();
    }
}
