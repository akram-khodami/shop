<?php

namespace App\Observers;

use App\Models\Brand;
use Illuminate\Support\Str;

class BrandObserver
{
    public function creating(Brand $brand)
    {
        $slug = Str::slug($brand->name);
        $count = Brand::where('slug', 'LIKE', "{$slug}%")->count();
        $brand->slug = $count ? "{$slug}-{$count}" : $slug;

        $brand->is_active = true;
    }

    public function updating(Brand $brand)
    {

    }
}
