<?php

namespace App\Observers;

use App\Models\Product;
use App\Services\BarcodeService;
use App\Services\SKUService;
use Illuminate\Support\Str;

class ProductObserver
{
    /**
     * Handle the Product "creating" event.
     */
    public function creating(Product $product): void
    {
        $slug = Str::slug($product->name);
        $count = Product::where('slug', 'LIKE', "{$slug}%")->count();
        $product->slug = $count ? "{$slug}-{$count}" : $slug;//althugh it is unique in database

        $product->sku = SKUService::generateSKU($product->name);

        $product->barcode = BarcodeService::generateBarcode();
    }

    public function updating(Product $product): void
    {
        if ($product->isDirty('name')) {//user should know that it is not good for SEO

            $slug = Str::slug($product->name);
            $count = Product::where('slug', 'LIKE', "{$slug}%")->count();
            $product->slug = $count ? "{$slug}-{$count}" : $slug;

        }
    }

    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {

    }


    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        //
    }
}
