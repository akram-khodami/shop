<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return response()->json(
            [
                'categories' => Category::query()
                    ->where('depth', '=', 0)
                    ->select(['id', 'name', 'slug'])
                    ->get(),
                'popularBrands' => []

            ]);
    }
}
