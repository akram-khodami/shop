<?php

namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use App\Models\Cart;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCartNotEmpty
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ?string $relations = null): Response
    {
        $cart = Cart::where('user_id', auth()->id())->where('is_active', true)->with('items')->first();

        if (!$cart || $cart->items->isEmpty()) {

            throw  new ApiException('سبد خرید خالی است', [], 400);

        }

        if ($relations !== 'none') {

            $this->loadCartRelations($cart);

        }

        $request->merge(['cart' => $cart]);

        return $next($request);


    }

    protected function loadCartRelations(Cart $cart): void
    {
        $cart->load(
            [
                'items.product' => fn ($query) => $query->select('id', 'name', 'slug', 'price'),
                'items.product.primaryImage',
            ]);
    }
}
