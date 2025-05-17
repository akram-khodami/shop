<?php


namespace App\Repositories;


use App\Models\Cart;

class CartRepository extends BaseRepository
{

    protected function model(): string
    {
        return Cart::class;
    }
}
