<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\Admin\ProductResource;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use http\Env\Response;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use function League\Flysystem\has;
use function Nette\Utils\attributes;
use function Ramsey\Collection\remove;

class ProductController extends Controller
{
    #filter???
    public function index()
    {
        //???filter that working with get method http

        $products = Product::Paginate(10);

        return ProductResource::collection($products);

    }

    public function create()
    {
        $categories = Category::all();

        return response()->json(
            [
                'categories' => $categories,
                'default_values' => [
                    'price' => 0,
                    'stock' => 0,
                ]
            ], 200);
    }

    public function store(StoreProductRequest $request)
    {
        try {

            return DB::transaction(function () use ($request) {

                //===step0: prepare data
                $validatedData = $request->validated();

                $images = $validatedData['images'] ?? null;
                $categories = $validatedData['categories'] ?? null;
                $attributes = $validatedData['attributes'] ?? null;

                unset($validatedData['images']);
                unset($validatedData['categories']);
                unset($validatedData['attributes']);

                //===step1
                $product = Product::create($validatedData);
//                $product = Product::create($request->except(['images', 'categories', 'attributes']));

                //===step2
                $product->categories()->attach($request->categories);

                //===step3
                $product->attributes()->attach($attributes);//attributes format example: [['atrribute_id'=>1,'value'=>'red'],['atrribute_id'=>2,'value'=>'seramic']]

                //===step4
                if ($request->has('images')) {

                    $this->uploadImages($product, $images);

                    $product->load('images');

                }

                $product->load('categories');
                $product->load('attributes');

                return new ProductResource($product);

            });

        } catch (QueryException $e) {

            Log::error("Database error while storing product: " . $e->getMessage());

            return $this->errorResponse('An error occurred.', $e);

        } catch (\Exception $e) {

            Log::error("Unexpected error while storing product: " . $e->getMessage());

            return $this->errorResponse('unexpected error while storing product', $e);

        }

    }

    public function show(Product $product)
    {
        return new ProductResource($product);

    }

    public function edit()
    {
        $categories = Category::all();

        return response()->json(
            [
                'categories' => $categories,
            ], 200);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {

        try {

            return DB::transaction(function () use ($request, $product) {

                //===step0: prepare data
                $validatedData = $request->validated();

                $images = $validatedData['images'] ?? null;
                $categories = $validatedData['categories'] ?? null;
                $attributes = $validatedData['attributes'] ?? null;

                unset($validatedData['images']);
                unset($validatedData['categories']);
                unset($validatedData['attributes']);

                //===step1
                $product->update($validatedData);

                //===step2
                $product->categories()->sync($request->categories);

                //===step3
                $product->attributes()->sync($attributes);//attributes format example: [['atrribute_id'=>1,'value'=>'red'],['atrribute_id'=>2,'value'=>'seramic']]

                //===step4
                if ($request->has('images') && $request->file('images')) {

                    $this->uploadImages($product, $images);

                    $product->load('images');

                }

                $product->load('categories');
                $product->load('attributes');

                return new ProductResource($product);

            });

        } catch (QueryException $e) {

            Log::error("Database error while storing product: " . $e->getMessage());

            return $this->errorResponse('An error occurred.', $e);

        } catch (\Exception $e) {

            Log::error("Unexpected error while updating product: " . $e->getMessage());

            return $this->errorResponse('unexpected error while updating product', $e);

        }

    }

    public function destroy(Product $product)
    {
        try {

            return DB::transaction(function () use ($product) {

                //step1:detach categories
                $product->categories()->detach();

                //step2:detach attributes
                $product->attributes()->detach();

                //===step3:remove images
                if ($product->images()->exists()) {

                    $this->deleteImages($product);

                }

                //step4:delete row
                $product->delete();

                //===
                return response()->json(
                    [
                        'message' => 'product is deleted',
                    ], 200);
            });

        } catch (QueryException $e) {

            Log::error("Database error while deleting product: " . $e->getMessage());

            return $this->errorResponse('An error occurred.', $e);

        } catch (\Exception $e) {

            Log::error("Unexpected error while deleting product: " . $e->getMessage());

            return $this->errorResponse('unexpected error while updating product', $e);

        }
    }

    protected function deleteImages(Product $product)
    {
        foreach ($product->images as $image) {
            Storage::disk($image->disk)->delete($image->image_path);
            //???delete folder with name $product-id
        }
        $product->images()->delete();
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

    protected function errorResponse(string $message, \Exception $e, int $status = 500)
    {
        return response()->json(
            [
                'message' => $message,
                'error' => config('app.debug') ? $e->getMessage() : null
            ], $status);
    }

    public function setPrimaryImage(Product $product, ProductImage $image)
    {
        try {

            DB::transaction(function () use ($product, $image) {
                $product->images()->update(['is_primary' => false]);
                $image->update(['is_primary' => true]);
            });

            return response()->json([$image]);//ProductImageResource???

        } catch (\Exception $e) {

            Log::error("Set primary image error: " . $e->getMessage());
            return $this->errorResponse('erro in setting image', $e);

        }

    }

}
