<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\BrandResource;
use App\Models\Brand;
use App\Models\Product;
use App\Services\BrandService;
use App\Services\ProductService;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    protected $brandService;
    protected $productService;

    public function __construct(BrandService $brandService, ProductService $productService)
    {
        $this->brandService = $brandService;
        $this->productService = $productService;
    }

    public function index()
    {
        $brands = $this->brandService->all();

        return BrandResource::collection($brands);
    }

    public function show(Brand $brand)
    {
        return new BrandResource($brand);
    }

    public function products(Brand $brand)
    {
        $products = $this->brandService->brandProducts($brand);

        return response()->json(
            [
                $brand, $products

            ]);
    }
}
