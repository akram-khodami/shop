<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\ApiException;
use App\Exceptions\MyApiQueryException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\CategoryResource;
use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::paginate(10);

        return CategoryResource::collection($categories);

    }

    public function store(StoreCategoryRequest $request)
    {
        try {

            return DB::transaction(function () use ($request) {

                $category = Category::create($request->validated());

                if ($request->hasFile('icon')) {

                    $path = $this->uploadIcon($request, $category);

                    $category->update(['icon' => $path]);

                }

                return new CategoryResource($category);

            });

        } catch (QueryException $e) {

            Log::error("Database error while storing category: " . $e->getMessage());

            throw new MyApiQueryException('An error occurred.', $e, 500);

        } catch (\Exception $e) {

            Log::error("Unexpected error while storing category: " . $e->getMessage());

            throw new ApiException('unexpected error while storing category', $e, 500);

        }

    }

    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        try {

            return DB::transaction(function () use ($request, $category) {

                $category->update($request->validated());

                if ($request->hasFile('icon')) {

                    $path = $this->uploadIcon($request, $category);

                    $category->update(['icon' => $path]);

                }

                return new CategoryResource($category);

            });

        } catch (QueryException $e) {

            Log::error("Database error while updating category: " . $e->getMessage());

            throw new MyApiQueryException('An error occurred.', $e, 500);

        } catch (\Exception $e) {

            Log::error("Unexpected error while updating category: " . $e->getMessage());

            throw new ApiException('unexpected error while storing category', $e, 500);

        }
    }

    public function destroy(Category $category)
    {
        try {

            return DB::transaction(function () use ($category) {

                if ($category->icon && Storage::disk('public')->exists($category->icon)) {

                    Storage::disk('public')->delete($category->icon);

                }

                $category->delete();

                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Category deleted successfully',
                    ]);

            });

        } catch (QueryException $e) {

            Log::error("Database error while deleting category: " . $e->getMessage());

            throw new MyApiQueryException('Database error occurred while deleting category', $e, 500);

        } catch (\Exception $e) {

            Log::error("Unexpected error while deleting category: " . $e->getMessage());

            throw new ApiException('Unexpected error occurred while deleting category', $e, 500);

        }
    }

    private function uploadIcon($request, $category)
    {
        $icon = $request->file('icon');

        $iconName = $category->slug . '.' . $icon->getClientOriginalExtension();

        //remove preview icon
        if ($category->icon && Storage::disk('public')->exists($category->icon)) {

            Storage::disk('public')->delete($category->icon);

        }

        $path = $icon->storeAs('categories', $iconName, 'public');

        return $path;
    }
}
