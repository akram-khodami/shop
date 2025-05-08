<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // دسترسی به URL کامل تصویر
    public function getUrlAttribute()
    {
        return Storage::disk($this->disk)->url($this->path);
    }
}
