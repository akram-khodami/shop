<?php


namespace App\Repositories;


use App\Models\Product;

class ProductRepository
{
    public function Paginate()
    {
        return Product::Paginate(10);
    }

    public function all()
    {
        return Product::all();
    }

    public function store(array $data)
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data)
    {
        return $product->update($data);
    }

    public function destroy(Product $product)
    {
        return $product->delete();
    }
}
