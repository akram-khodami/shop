<?php

namespace App\Observers;

use App\Exceptions\ApiException;
use App\Exceptions\CategoryDeletionException;
use App\Models\Category;
use Exception;
use Illuminate\Support\Str;

class CategoryObserver
{
    /**
     * Handle the Product "creating" event.
     */
    public function creating(Category $category): void
    {
        $slug = Str::slug($category->name);
        $count = Category::where('slug', 'LIKE', "{$slug}%")->count();
        $category->slug = $count ? "{$slug}-{$count}" : $slug;

        if (is_null($category->parent_id)) {

            $category->depth = 0;

        } else {

            $parent = Category::find($category->parent_id);

            $category->depth = $parent->depth + 1;

        }

        if ($category->depth > 4) {

            throw new \Exception('we have at most 4 level of category.');

        }

    }

    /**
     * Handle the Category "created" event.
     */
    public function created(Category $category): void
    {
        //
    }

    /**
     * Handle the Category "updated" event.
     */
    public function updated(Category $category): void
    {
        //
    }

    public function deleting(Category $category)
    {
        if ($category->children()->exists()) {

            throw new ApiException(
                'امکان حذف دسته‌بندی‌های دارای زیرمجموعه وجود ندارد',
                ['children' => $category->children()->count()], 403
            );

        }
    }

    /**
     * Handle the Category "deleted" event.
     */
    public function deleted(Category $category): void
    {
        //
    }

    /**
     * Handle the Category "restored" event.
     */
    public function restored(Category $category): void
    {
        //
    }

    /**
     * Handle the Category "force deleted" event.
     */
    public function forceDeleted(Category $category): void
    {
        //
    }
}
