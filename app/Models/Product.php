<?php

namespace App\Models;

use App\Observers\ProductObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([ProductObserver::class])]
class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $guarded = [];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'product_attribute_values')->withTimestamps();
    }

    //???
    public function cartItem()
    {
        return $this->hasMany(CartItem::class);
    }

    //???
    public function orderItem()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

}
