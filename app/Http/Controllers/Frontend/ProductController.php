<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use function Illuminate\Support\query;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        $products = Product::query()
            ->select('products.id', 'name', 'slug')
            ->with(
                [
                    'images' => function ($query) {
                        $query->where('is_primary', true)
                            ->select('product_id', 'is_primary', 'image_path')
                            ->first();
                    }
                ])->paginate();

        return response()->json($products);

    }

    public function show(Product $product)
    {
        return new ProductResource($product);

    }

    public function related(Product $product)
    {

    }


}
