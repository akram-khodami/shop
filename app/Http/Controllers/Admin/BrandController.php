<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\ApiException;
use App\Exceptions\MyApiQueryException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Http\Resources\Admin\BrandResource;
use App\Models\Brand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::paginate(10);

        return BrandResource::collection($brands);
    }

    public function store(StoreBrandRequest $request)
    {
        try {

            return DB::transaction(function () use ($request) {

                $brand = Brand::create($request->validated());

                if ($request->hasFile('logo')) {

                    $path = $this->uploadLogo($request, $brand);

                    $brand->update(['logo' => $path]);

                }

                return new BrandResource($brand);

            });

        } catch (QueryException $e) {

            Log::error("Database error while storing brand: " . $e->getMessage());

            throw new MyApiQueryException('An error occurred.', $e, 500);

        } catch (\Exception $e) {

            Log::error("Unexpected error while storing brand: " . $e->getMessage());

            throw new ApiException('unexpected error while storing brand', $e, 500);

        }

    }

    public function show(Brand $brand)
    {
        return new BrandResource($brand);
    }

    public function update(UpdateBrandRequest $request, Brand $brand)
    {
        try {

            return DB::transaction(function () use ($request, $brand) {

                $brand->update($request->validated());

                if ($request->hasFile('logo')) {

                    $path = $this->uploadLogo($request, $brand);

                    $brand->update(['logo' => $path]);

                }

                return new BrandResource($brand);

            });

        } catch (QueryException $e) {

            Log::error("Database error while updating brand: " . $e->getMessage());

            throw new MyApiQueryException('An error occurred.', $e, 500);

        } catch (\Exception $e) {

            Log::error("Unexpected error while updating brand: " . $e->getMessage());

            throw new ApiException('unexpected error while storing brand', $e, 500);

        }

    }

    public function destroy(Brand $brand)
    {
        try {

            DB::transaction(function () use ($brand) {

                if ($brand->logo && Storage::disk('public')->exists($brand->logo)) {

                    Storage::disk('public')->delete($brand->logo);

                }

                $brand->delete();

                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Brand deleted successfully',
                    ]);

            });

        } catch (QueryException $e) {

            Log::error("Database error while deleting brand: " . $e->getMessage());

            throw new MyApiQueryException('Database error occurred while deleting brand', $e, 500);

        } catch (\Exception $e) {

            Log::error("Unexpected error while deleting brand: " . $e->getMessage());

            throw new ApiException('Unexpected error occurred while deleting brand', $e, 500);

        }


    }

    private function uploadLogo($request, Brand $brand)
    {
        $logo = $request->file('logo');

        $logoName = $brand->slug . '.' . $logo->getClientOriginalExtension();

        //remove preview logo
        if ($brand->logo && Storage::disk('public')->exists($brand->logo)) {

            Storage::disk('public')->delete($brand->logo);
        }

        $path = $logo->storeAs('brands', $logoName, 'public');

        return $path;
    }
}
