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
    protected $categorieservice;

    public function __construct(Categorieservice $categorieservice)
    {
        $this->categorieservice = $categorieservice;
    }

    /**
     * @OA\Get(
     *     path="/api/admin/categories",
     *     tags={"Categories"},
     *     summary="categories list",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="get and show list of categories in manage categories page",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Category")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $categories = $this->categorieservice->paginate();

        return CategoryResource::collection($categories);

    }

    /**
     * Store a newly created Category
     *
     * @OA\Post(
     *     path="/api/admin/categories",
     *     tags={"Categories"},
     *     summary="Create a new Category",
     *     description="Stores a new Category in the database",
     *     operationId="storeCategory",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Category data",
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Arts Crafts Sewing"),
     *             @OA\Property(property="description", type="string", example="Arts, Crafts & Sewing›Sewing›Sewing Project Kits", nullable=true),
     *             @OA\Property(property="parent_id", type="integer", example="", nullable=true),
     *             @OA\Property(property="icon", type="string", format="binary", description="Category icon image", nullable=true)
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

        $category = $this->categorieservice->store($request->validated(), $request->hasFile('icon'), $request->file('icon'));

        return new CategoryResource($category);

    }

    /**
     * @OA\Get(
     *     path="/api/admin/categories/{id}",
     *     tags={"Categories"},
     *     security={{"bearerAuth": {}}},
     *     summary="show a category",
     *     @OA\Response(
     *         response=200,
     *         description="Successful request response",
     *         @OA\JsonContent(ref="#/components/schemas/CategoryResource")
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
    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/categories/{id}",
     *     tags={"Categories"},
     *     security={{"bearerAuth": {}}},
     *     summary="update a category",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Category data",
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Arts Crafts Sewing"),
     *             @OA\Property(property="description", type="string", example="Arts, Crafts & Sewing›Sewing›Sewing Project Kits", nullable=true),
     *             @OA\Property(property="parent_id", type="integer", example="", nullable=true),
     *             @OA\Property(property="icon", type="string", format="binary", description="Category icon image", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful request response",
     *         @OA\JsonContent(ref="#/components/schemas/CategoryResource")
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
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category = $this->categorieservice->update($category, $request->validated(), $request->hasFile('icon'), $request->file('icon'));

        return new CategoryResource($category);

    }

    /**
     * @OA\Delete(
     *     path="/api/admin/categories/{id}",
     *     tags={"Categories"},
     *     security={{"bearerAuth": {}}},
     *     summary="remove a category",
     *     @OA\Response(
     *         response=200,
     *         description="Successful request response",
     *         @OA\JsonContent(ref="#/components/schemas/CategoryResource")
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
     *   ),
     */
    public function destroy(Category $category)
    {
        $this->categorieservice->destroy($category);

        return response()->json(
            [
                'success' => true,
                'message' => 'Category deleted successfully',
            ]);

    }


}
