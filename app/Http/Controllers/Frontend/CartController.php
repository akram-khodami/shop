<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCartRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            [
                'success' => true,
                'message' => 'cart items:',
                'data' => $request->cart,
                'total' => $this->calculateCartTotal($request->cart)
            ]);

        return response()->json(['data' => $request->cart]);
    }

    public function add(Product $product)
    {
        DB::transaction(function () use ($product) {

            $cart = $this->getOrCreateCart();

            $existingItem = $cart->items()->where('product_id', $product->id)->first();

            if ($existingItem) {

//                $existingItem->increment('quantity');
                $existingItem->update(['quantity' => $existingItem->quantity + 1, 'price' => $product->price]);

            } else {

                CartItem::create(
                    [
                        'cart_id' => $cart->id,
                        'product_id' => $product->id,
                        'quantity' => 1,
                        'price' => $product->price
                    ]);

            }

        });

        $userActiveCart = $this->getUserActiveCart();

        return response()->json(
            [
                'success' => true,
                'message' => 'محصول به سبد خرید اضافه شد',
                'data' => $userActiveCart,
                'total' => $this->calculateCartTotal($userActiveCart),
            ]);

    }

    public function remove(Request $request, Product $product)
    {
        $message = DB::transaction(function () use ($request, $product) {

            if ($request->cartItem->quantity <= 1) {

                $request->cartItem->delete();//???

                $message = 'محصول از سبد خرید حذف شد.';

            } else {

//                $request->cartItem->decrement('quantity');
                $request->cartItem->update(['quantity' => $request->cartItem->quantity - 1, 'price' => $product->price]);

                $message = 'تعداد محصول در سبد خرید کاهش یافت. ';

            }

            if ($request->cart->fresh()->items()->count() == 0) {

                $request->cart->delete();

                $message .= 'سبد خرید خالی شد';

            }

            return $message;

        });


        return response()->json(
            [
                'success' => true,
                'message' => $message,
                'data' => $this->getUserActiveCart(),
                'total' => $this->calculateCartTotal($request->cart),
            ]);

    }

    public function update(UpdateCartRequest $request, Product $product)
    {
        $request->cartItem->update(['quantity' => $request->quantity]);

        return response()->json(
            [
                'success' => true,
                'message' => 'تعداد محصول به‌روزرسانی شد',
                'data' => $this->getUserActiveCart(),
                'total' => $this->calculateCartTotal($request->cart),
            ]);

    }

    public function clear(Request $request)
    {

        DB::transaction(function () use ($request) {

            $request->cart->items()->delete();

            $request->cart->delete();

        });

        return response()->json(
            [
                'success' => true,
                'message' => 'سبد خرید با موفقیت خالی شد.',
            ]);

    }

    protected function getUserActiveCart($withRelations = true)
    {
        $cart = Cart::where('user_id', auth()->id())->where('is_active', true);

        if ($withRelations) {

            $cart->with(
                [
                    'items.product' => function ($query) {
                        $query->select('id', 'name', 'slug', 'price');
                    },
                    'items.product.primaryImage',
                ]);

        }

        $cart = $cart->first();

        return $cart;
    }

    protected function getOrCreateCart()
    {
        return Cart::firstOrCreate(
            ['user_id' => auth()->id(), 'is_active' => true]
        );
    }

    protected function calculateCartTotal(Cart $cart)
    {
        return $cart->items->sum(fn ($item) => $item->quantity * $item->price);
    }

}
