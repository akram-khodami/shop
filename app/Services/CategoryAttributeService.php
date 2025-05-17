<?php


namespace App\Services;


use App\Http\Resources\Admin\AttributeResource;
use App\Http\Resources\Admin\CategoryResource;
use App\Models\Attribute;
use App\Models\Category;

class CategoryAttributeService
{
    public function showCategoryAttributesPageData(Category $category)
    {
        return [
            'category' => $category,
            'attributes' => Attribute::query()->orderBy('name')->get(),
            'assignedCount' => $category->attributes()->count(),
            'assignedAttributeIds' => $category->attributes()->pluck('attributes.id')->toArray(),
        ];

    }
}
