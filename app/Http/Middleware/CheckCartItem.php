<?php

namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCartItem
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $product = $request->route('product');//in model binding it is object

        $cartItem = $request->cart->items->where('product_id', $product->id)->first();

        if (!$cartItem) {

            throw  new ApiException('این محصول در سبد خرید وجود ندارد', [], 400);

        }

        $request->merge(['cartItem' => $cartItem]);

        return $next($request);
    }
}
