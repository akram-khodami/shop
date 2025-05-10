<?php

namespace App\Http\Controllers\Frontend;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Frontend\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingMethod;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function checkout(Request $request)
    {
        $request->validate(
            [
                'shippingMethod' => 'required|exists:shipping_methods,id',
            ]);

        $shippingMethod = ShippingMethod::findOrFail($request->shippingMethod);

        try {

            $order = DB::transaction(function () use ($request, $shippingMethod) {

                // Create order
                $order = Order::create(
                    [
                        'order_number' => Order::generateOrderNumber(),
                        'shipping_amount' => $shippingMethod->base_price,
                        'user_id' => $request->cart->user_id,
                        'shipping_method_id' => $shippingMethod->id,
                        'total_price' => $request->cart->items->sum(fn ($item) => $item->quantity * $item->price) + $shippingMethod->base_price,
                        'status' => 'pending',
                    ]);

                // Prepare order items
                $orderItems = $request->cart->items->map(function ($item) use ($order) {

                    return [
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                })->toArray();

                // Insert all items at once
                OrderItem::insert($orderItems);

                // Deactivate cart
                $request->cart->update(['is_active' => false]);

                return $order;

            });

        } catch (QueryException $e) {

            Log::error("Database error: " . $e->getMessage());


            throw new ApiException('An error occurred.' . config('app.debug') ? $e->getMessage() : nul, NULL, 500);

        } catch (\Exception $e) {

            Log::error("Unexpected error while storing product: " . $e->getMessage());

            throw new ApiException('unexpected error while storing product', NULL, 500);

        }

        return response()->json(['success' => true, 'data' => $order->load('items')]);
    }

    public function index()
    {
        $orders = Order::with('items')->where('user_id', auth()->id())->paginate(10);

        return OrderResource::collection($orders);

    }

    public function show(Order $order)
    {

        return new OrderResource($order);

    }

    public function cancel(Order $order)
    {
        $order->update(['status' => 'cancel']);

        return new OrderResource($order);

    }
}
