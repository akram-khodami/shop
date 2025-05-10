<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\CategoryResource;
use App\Http\Resources\Admin\ProductResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::whereNull('parent_id')->get();

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
        $products = $category->products()->select('products.id', 'name', 'slug')
            ->with(
                [
                    'images' => function ($query) {
                        $query->where('is_primary', true)
                            ->select('product_id', 'is_primary', 'image_path')
                            ->first();
                    }
                ])->get()
            ->makeHidden('pivot');

        return response()->json($products);
    }
}
