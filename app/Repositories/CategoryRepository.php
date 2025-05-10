<?php


namespace App\Repositories;


use App\Models\Category;

class CategoryRepository
{
    public function paginate()
    {
        return Category::paginate(10);
    }

    public function all()
    {
        return Category::all();
    }

    public function create(array $data)
    {
        return Category::create($data);
    }

    public function update(Category $category, array $data)
    {
        return $category->update($data);
    }

    public function destroy(Category $category)
    {
        return $category->delete();
    }
}
