<?php


namespace App\Services;


use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CategoryService
{
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function all()
    {
        return $this->categoryRepository->all();
    }

    public function paginate()
    {
        return $this->categoryRepository->paginate();
    }

    public function store(array $validatedData, bool $hasIcon, $icon)
    {
        return DB::transaction(function () use ($validatedData, $hasIcon, $icon) {

            $category = $this->categoryRepository->create($validatedData);

            if ($hasIcon) {

                $path = $this->uploadIcon($icon, $category);

                $category->update(['icon' => $path]);

            }

            return $category;

        });
    }

    public function update(Category $category, array $validatedData, bool $hasIcon, $icon)
    {
        return DB::transaction(function () use ($category, $validatedData, $hasIcon, $icon) {

            $this->categoryRepository->update($category, $validatedData);

            if ($hasIcon) {

                $path = $this->uploadIcon($icon, $category);

                $category->update(['icon' => $path]);

            }
            return $category;

        });
    }

    private function uploadIcon($icon, $category)
    {

        $iconName = $category->slug . '.' . $icon->getClientOriginalExtension();

        //remove preview icon
        if ($category->icon && Storage::disk('public')->exists($category->icon)) {

            Storage::disk('public')->delete($category->icon);

        }

        $path = $icon->storeAs('categories', $iconName, 'public');

        return $path;
    }

    public function destroy(Category $category)
    {
        return DB::transaction(function () use ($category) {

            if ($category->icon && Storage::disk('public')->exists($category->icon)) {

                Storage::disk('public')->delete($category->icon);

            }

            $this->categoryRepository->delete($category);

        });
    }

    public function detachAttribute(Category $category, $id)
    {
        $category->attributes()->detach($id);

    }

    public function firstAncestor()
    {
        return $this->categoryRepository->getfirstAncestor();
    }

    public function getCategoryProducts(Category $category)
    {
        return $this->categoryRepository->getCategoryProducts($category);
    }
}
