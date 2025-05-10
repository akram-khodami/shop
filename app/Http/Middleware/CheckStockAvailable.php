<?php

namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckStockAvailable
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $product = $request->route('product');//in model binding it is object

        $quantity = $request->quantity ?? 1;//in add request it is 1

        if ($product->stock < $quantity) {

            throw new ApiException('موجودی کافی نیست', ['maxChoice' => $product->stock], 422);

        }

        return $next($request);

    }
}
