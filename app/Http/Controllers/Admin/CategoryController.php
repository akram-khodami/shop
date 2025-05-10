<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\CategoryResource;
use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Services\CategoryService;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        $categories = $this->categoryService->paginate();

        return CategoryResource::collection($categories);

    }

    public function store(StoreCategoryRequest $request)
    {

        $category = $this->categoryService->store($request->validated(), $request->hasFile('icon'), $request->file('icon'));

        return new CategoryResource($category);

    }

    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category = $this->categoryService->update($category, $request->validated(), $request->hasFile('icon'), $request->file('icon'));

        return new CategoryResource($category);

    }

    public function destroy(Category $category)
    {
        $this->categoryService->destroy($category);

        return response()->json(
            [
                'success' => true,
                'message' => 'Category deleted successfully',
            ]);

    }


}
