<?php

namespace App\Services;

use App\Models\Brand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Repositories\BrandRepository;

class BrandService
{
    protected $brandRepository;

    public function __construct(BrandRepository $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    public function all()
    {
        return $this->brandRepository->paginate(10);
    }

    public function store($request)
    {
        return DB::transaction(function () use ($request) {

            $brand = $this->brandRepository->create($request->validated());

            if ($request->hasFile('logo')) {

                $path = $this->uploadLogo($request, $brand);

                $this->brandRepository->update($brand, ['logo' => $path]);

            }

            return $brand;

        });
    }

    public function update($request, Brand $brand)
    {
        return DB::transaction(function () use ($request, $brand) {

            $brand->update($request->validated());

            if ($request->hasFile('logo')) {

                $path = $this->uploadLogo($request, $brand);

                $brand->update(['logo' => $path]);

            }

            return $brand;

        });
    }

    public function destroy(Brand $brand)
    {
        DB::transaction(function () use ($brand) {


            if ($brand->logo && Storage::disk('public')->exists($brand->logo)) {

                Storage::disk('public')->delete($brand->logo);

            }

            $brand = $this->brandRepository->delete($brand);

            return $brand;

        });
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

    public function brandProducts(Brand $brand)
    {
        return $this->brandRepository->getBrandProducts($brand);
    }

}
