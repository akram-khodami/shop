<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\Admin\ProductResource;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\CategoryService;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductController extends Controller
{
    private $productService;
    private $categoryService;

    public function __construct(ProductService $productService, CategoryService $categoryService)
    {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
    }

    public function index(): JsonResource
    {
        $products = $this->productService->paginate();

        return ProductResource::collection($products);
    }

    public function create(): JsonResponse
    {
        $categories = $this->categoryService->all();

        return response()->json(
            [
                'categories' => $categories,
                'default_values' => [
                    'price' => 0,
                    'stock' => 0,
                ]
            ], 200);
    }

    public function store(StoreProductRequest $request): JsonResource
    {
        $product = $this->productService->store($request->validated(), $request->has('images'));

        return new ProductResource($product);

    }

    public function show(Product $product): JsonResource
    {
        return new ProductResource($product);

    }

    public function edit(): JsonResponse
    {
        $categories = $this->categoryService->all();

        return response()->json(
            [
                'categories' => $categories,
            ], 200);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {

        $product = $this->productService->update($request->validated(), $product, $request->has('images'));

        return new ProductResource($product);

    }

    public function destroy(Product $product)
    {
        $this->productService->destroy($product);

        return response()->json(
            [
                'message' => 'product is deleted',
            ], 200);

    }

    public function setPrimaryImage(Product $product, ProductImage $productImage)
    {

        if ($product->id != $productImage->product_id) {

            throw new ApiException('this image does not belong to this product' . $productImage->product_id, [], 403);

        }
        $this->productService->setPrimaryImage($product, $productImage);

        return response()->json([$productImage]);

    }

}
