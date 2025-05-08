<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttributeValue extends Model
{
    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
