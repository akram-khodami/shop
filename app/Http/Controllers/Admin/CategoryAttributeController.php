<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\AttributeResource;
use App\Http\Resources\Admin\CategoryResource;
use App\Models\Attribute;
use App\Models\Category;
use App\Services\CategoryAttributeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use function Carbon\toArray;
use function Illuminate\Auth\Access\resource;

class CategoryAttributeController extends Controller
{
    private $categoryAttributeService;

    public function __construct(CategoryAttributeService $categoryAttributeService)
    {
        $this->categoryAttributeService = $categoryAttributeService;
    }

    public function index(Category $category): JsonResponse
    {
        $data = $this->categoryAttributeService->showCategoryAttributesPageData($category);

        return response()->json(
            [
                'data' => [
                    'category' => new CategoryResource($category),
                    'attributes' => AttributeResource::collection($data['attributes']),
                    'assignedAttributeIds' => $data['assignedAttributeIds'],
                ],
                'meta' => [
                    'assignedCount' => $data['assignedCount'],
                ]
            ]);
    }

    /**
     * اختصاص ویژگی‌ها به دسته‌بندی
     *
     * @param Request $request
     * @param Category $category
     * @return JsonResponse
     */
    public function store(Request $request, Category $category): JsonResponse
    {
        $validated = $request->validate([
            'attribute_ids' => 'required|array',
            'attribute_ids.*' => ['integer', 'exists:attributes,id']
        ]);

        //===تاریخ ها ثبت شوند یا کلا ستون های تاریخ در جدول واسط حذف شوند
        $category->attributes()->syncWithoutDetaching($validated['attribute_ids']);//اتصال‌های قبلی باید باقی بمونن و داده‌های جدید اضافه بشن
//        $category->attributes()->sync($validated['attribute_ids']);//همهٔ اتصال‌های قبلی باید پاک بشن و فقط داده‌های جدید بمونه

        return response()->json(
            [
                'message' => 'attributes are assigned to category.',
                'data' => [
                    'assignedAttributeIds' => $category->fresh()->attributes->pluck('id'),
                    $validated['attribute_ids']
                ],
                'meta' => [
                    'newlyAdded' => $validated['attribute_ids'],
                    'totalAssignedNow' => $category->fresh()->attributes()->count()
                ]
            ], 201);

    }

    /**
     * حذف ارتباط ویژگی با دسته‌بندی
     *
     * @param Category $category
     * @param Attribute $attribute
     * @return JsonResponse
     */
    public function destroy(Category $category, Attribute $attribute): JsonResponse
    {
        $category->attributes()->detach($attribute->id);

        return response()->json(
            [
                'message' => 'category attribute is deleted.',
                'data' => [
                    'remainingAttributeIds' => $category->fresh()->attributes->pluck('id')
                ],
                'meta' => [
                    'removedAttributeId' => $attribute->id,
                    'remainingCount' => $category->fresh()->attributes()->count()
                ]
            ]);

    }
}
