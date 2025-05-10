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

    public function index()
    {
        $brands = $this->brandService->all();

        return BrandResource::collection($brands);
    }

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
