<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\CategoryResource;
use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Services\Categorieservice;

class CategoryController extends Controller
{
    protected $Categorieservice;

    public function __construct(Categorieservice $Categorieservice)
    {
        $this->Categorieservice = $Categorieservice;
    }

    /**
     * @OA\Get(
     *     path="/api/admin/categories",
     *     tags={"Category"},
     *     security={{"bearerAuth": {}}},
     *     summary="دریافت لیست دسته بندی ها",
     *     @OA\Response(
     *         response=200,
     *         description="لیست دسته بندی ها"
     *     )
     * )
     */
    public function index()
    {
        $categories = $this->Categorieservice->paginate();

        return CategoryResource::collection($categories);

    }

    /**
     * Store a newly created Category
     *
     * @OA\Post(
     *     path="/api/admin/Categories",
     *     tags={"Category"},
     *     summary="Create a new Category",
     *     description="Stores a new Category in the database",
     *     operationId="storeCategory",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Category data",
     *         @OA\JsonContent(
     *             required={"name", "slug"},
     *             @OA\Property(property="name", type="string", example="ORIGINAL"),
     *             @OA\Property(property="slug", type="string", example="original"),
     *             @OA\Property(property="description", type="string", example="ORIGINAL company", nullable=true),
     *             @OA\Property(property="is_active", type="boolean", example=true, nullable=true),
     *             @OA\Property(property="logo", type="string", format="binary", description="Category logo image", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Category created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/CategoryResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     type="array",
     *                     @OA\Items(type="string", example="The name field is required.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Internal server error occurred.")
     *         )
     *     )
     * )
     */
    public function store(StoreCategoryRequest $request)
    {

        $category = $this->Categorieservice->store($request->validated(), $request->hasFile('icon'), $request->file('icon'));

        return new CategoryResource($category);

    }

    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/categories",
     *     tags={"Category"},
     *     security={{"bearerAuth": {}}},
     *     summary="دریافت لیست دسته بندی ها",
     *     @OA\Response(
     *         response=200,
     *         description="لیست دسته بندی ها"
     *     )
     * )
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category = $this->Categorieservice->update($category, $request->validated(), $request->hasFile('icon'), $request->file('icon'));

        return new CategoryResource($category);

    }
    /**
     * @OA\Delete(
     *     path="/api/admin/categories/{id}",
     *     tags={"Category"},
     *     security={{"bearerAuth": {}}},
     *     summary="حذف دسته بندی ها",
     *     @OA\Response(
     *         response=200,
     *         description="حذف دسته بندی ها"
     *     )
     * )
     */
    public function destroy(Category $category)
    {
        $this->Categorieservice->destroy($category);

        return response()->json(
            [
                'success' => true,
                'message' => 'Category deleted successfully',
            ]);

    }


}
