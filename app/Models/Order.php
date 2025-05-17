<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function generateOrderNumber()
    {
        do {
            $number = 'ORD-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('order_number', $number)->exists());

        return $number;
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    #[Scope]
    protected function userPendingOrder(Builder $query): void
    {
        $query->where(
            [
                [
                    'user_id', '=', auth()->id()
                ],
                [
                    'status', '=', 'pending'
                ]
            ]);
    }
}
