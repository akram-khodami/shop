<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\BrandResource;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::paginate(10);

        return BrandResource::collection($brands);
    }

    public function show(Brand $brand)
    {
        return new BrandResource($brand);
    }

    public function products(Brand $brand)
    {
        $products = Product::where('brand', $brand->id)->paginate(10);

        return response()->json(
            [
                $brand, $products

            ]);
    }
}
