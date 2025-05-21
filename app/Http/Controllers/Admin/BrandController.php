<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Http\Resources\Admin\BrandResource;
use App\Models\Brand;
use App\Services\BrandService;

class BrandController extends Controller
{
    protected $brandService;

    public function __construct(BrandService $brandService)
    {
        $this->brandService = $brandService;
    }

    /**
     * @OA\Get(
     *     path="/api/admin/brands",
     *     summary="Brands list",
     *     tags={"Brands"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="get and show list of brands in manage brands page",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Brand")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $brands = $this->brandService->all();

        return BrandResource::collection($brands);
    }

    /**
     * Store a newly created brand
     *
     * @OA\Post(
     *     path="/api/admin/brands",
     *     tags={"Brands"},
     *     summary="Create a new brand",
     *     description="Stores a new brand in the database",
     *     operationId="storeBrand",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Brand data",
     *         @OA\JsonContent(
     *             required={"name", "slug"},
     *             @OA\Property(property="name", type="string", example="ORIGINAL"),
     *             @OA\Property(property="slug", type="string", example="original"),
     *             @OA\Property(property="description", type="string", example="ORIGINAL company", nullable=true),
     *             @OA\Property(property="is_active", type="boolean", example=true, nullable=true),
     *             @OA\Property(property="logo", type="string", format="binary", description="Brand logo image", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Brand created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/BrandResource")
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
    public function store(StoreBrandRequest $request)
    {
        $brand = $this->brandService->store($request);//parameter???

        return new BrandResource($brand);

    }

    public function show(Brand $brand)
    {
        return new BrandResource($brand);
    }

    public function update(UpdateBrandRequest $request, Brand $brand)
    {

        $brand = $this->brandService->update($request, $brand);

        return new BrandResource($brand);

    }

    public function destroy(Brand $brand)
    {
        $this->brandService->destroy($brand);

        return response()->json(
            [
                'success' => true,
                'message' => 'Brand deleted successfully',
            ]);
    }
}
