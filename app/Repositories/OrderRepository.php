<?php


namespace App\Repositories;


use App\Models\Order;

class OrderRepository extends BaseRepository
{
    protected function model(): string
    {
        return Order::class;
    }

    public function getUserPendingOrder()
    {
//        $order = $this->model()::userPendingOrder()->with('user')->first();//2 query

        $order = Order::userPendingOrder()
            ->select('orders.*')
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->first();//1 query

        return $order;
    }
}
