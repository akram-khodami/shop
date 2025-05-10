<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function index()
    {
        $shippingMethods = ShippingMethod::get();

        return response()->json(
            [
                'success' => true,
                'data' => $shippingMethods,
            ]);
    }

    public function show(ShippingMethod $shippingMethod)
    {
        return response()->json(
            [
                'success' => true,
                'data' => $shippingMethod,
            ]);
    }
}
