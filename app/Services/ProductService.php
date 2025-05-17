<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function paginate()
    {
        return $this->productRepository->paginate();
    }

    public function all()
    {
        return $this->productRepository->all();
    }

    public function store($validatedData, bool $hasImage)
    {
        return DB::transaction(function () use ($validatedData, $hasImage) {

            //===step0: prepare data
            $images = $validatedData['images'] ?? null;
            $categories = $validatedData['categories'] ?? null;
            $attributes = $validatedData['attributes'] ?? null;

            unset($validatedData['images']);
            unset($validatedData['categories']);
            unset($validatedData['attributes']);

            $product = $this->productRepository->create($validatedData);

            $product->categories()->attach($categories);

            $product->attributes()->attach($attributes);//attributes format example: [['atrribute_id'=>1,'value'=>'red'],['atrribute_id'=>2,'value'=>'seramic']]

            //===step4
            if ($hasImage) {

                $this->uploadImages($product, $images);

                $product->load('images');

            }

            $product->load('categories');
            $product->load('attributes');

            return $product;

        });

    }

    public function update(array $validatedData, Product $product, bool $hasImage)
    {
        return DB::transaction(function () use ($validatedData, $product, $hasImage) {

            $images = $validatedData['images'] ?? null;
            $categories = $validatedData['categories'] ?? null;
            $attributes = $validatedData['attributes'] ?? null;

            unset($validatedData['images']);
            unset($validatedData['categories']);
            unset($validatedData['attributes']);

            $this->productRepository->update($product, $validatedData);

            //===step2
            $product->categories()->sync($categories);

            //===step3
            $product->attributes()->sync($attributes);//attributes format example: [['atrribute_id'=>1,'value'=>'red'],['atrribute_id'=>2,'value'=>'seramic']]

            //===step4
            if ($hasImage && $images) {

                $this->uploadImages($product, $images);

                $product->load('images');

            }

            $product->load('categories');
            $product->load('attributes');

            return $product;

        });

    }

    public function destroy(Product $product)
    {
        return DB::transaction(function () use ($product) {

            $product->categories()->detach();

            $product->attributes()->detach();

            if ($product->images()->exists()) {

                $this->deleteImages($product);//???

            }

            $this->productRepository->delete($product);

        });

    }

    public function setPrimaryImage(Product $product, ProductImage $image)
    {
        DB::transaction(function () use ($product, $image) {

            $product->images()->update(['is_primary' => false]);

            $image->update(['is_primary' => true]);

        });

    }

    protected function uploadImages(Product $product, array $images)
    {
        $imageData = [];
        foreach ($images as $image) {
            $path = $image->store("products/{$product->id}", 'public');
            $imageData[] = [
                'image_path' => $path,
                'is_primary' => $product->images()->doesntExist(),//???It should be tested.
            ];
        }
        $product->images()->createMany($imageData);
    }

    protected function deleteImages(Product $product)
    {
        foreach ($product->images as $image) {
            Storage::disk($image->disk)->delete($image->image_path);
            //???delete folder with name $product-id
        }
        $product->images()->delete();
    }

}
