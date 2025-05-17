<?php


namespace App\Repositories;


use App\Models\Category;
use Illuminate\Database\Eloquent\Model;

class CategoryRepository extends BaseRepository
{
    public function model(): string
    {
        return Category::class;
    }

    public function getFirstAncestor()
    {
        return $this->model::whereNull('parent_id')->get();
    }

    public function getCategoryProducts(Category $category)
    {
        $products = $category->products()->select('products.id', 'name', 'slug')
            ->with(
                [
                    'images' => function ($query) {
                        $query->where('is_primary', true)
                            ->select('product_id', 'is_primary', 'image_path')
                            ->first();
                    }
                ])->get()
            ->makeHidden('pivot');

        return $products;
    }
}
