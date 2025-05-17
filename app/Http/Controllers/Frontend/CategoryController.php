<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\CategoryResource;
use App\Http\Resources\Admin\ProductResource;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        $categories = $this->categoryService->firstAncestor();

        return CategoryResource::collection($categories);

    }

    public function show(Category $category)
    {
        return response()->json(
            [
                'data' => new CategoryResource($category),
                'children' => $category->children()->get(),
                'parent' => $category->parent()->first(),
            ]);
    }

    public function products(Category $category)
    {
        $products = $this->categoryService->getCategoryProducts($category);

        return response()->json($products);
    }
}
