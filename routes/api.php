<?php

use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\BrandController as AdminBrandController;
use App\Http\Controllers\Admin\CategoryAttributeController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Frontend\BrandController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CategoryController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\InvoiceController;
use App\Http\Controllers\Frontend\OrderController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\PayController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\SearchController;
use App\Http\Controllers\Frontend\ShippingController;
use Illuminate\Support\Facades\Route;

Route::namespace('App\Http\Controllers\Frontend')->group(function () {
    // صفحه اصلی و صفحات استاتیک
    Route::get('/', [HomeController::class, 'index']);
    Route::get('/about', [PageController::class, 'about']);
    Route::get('/contact', [PageController::class, 'contact']);
    Route::post('/contact', [PageController::class, 'sendContactForm']);

// جستجو
    Route::get('/search', [SearchController::class, 'index']);

// دسته‌بندی‌ها و برندها
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);
    Route::get('/categories/{category}/products', [CategoryController::class, 'products']);

    Route::get('/brands', [BrandController::class, 'index']);
    Route::get('/brands/{brand}', [BrandController::class, 'show']);
    Route::get('/brands/{brand}/products', [BrandController::class, 'products']);

// محصولات
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{product}', [ProductController::class, 'show']);
    Route::get('/products/{product}/related', [ProductController::class, 'related']); // محصولات مرتب

// نظرات
//    Route::get('/products/{product}/reviews', [ReviewController::class, 'index']);
//    Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])->middleware('auth:sanctum');

// سبد خرید و سفارشات (نیاز به احراز هویت دارند)
    Route::middleware('auth:sanctum')->group(function () {
        // سبد خرید
        Route::get('/cart', [CartController::class, 'index'])->middleware('checkCart');//مشاهده سبد خرید
        Route::post('/cart/{product}', [CartController::class, 'add'])->middleware('checkStock');////افزودن به سبد خرید
        Route::patch('/cart/{product}', [CartController::class, 'update'])->middleware('checkCart:none', 'checkCartItem', 'checkStock');// مثل تغییر quantity
        Route::delete('/cart/{product}', [CartController::class, 'remove'])->middleware('checkCart:none', 'checkCartItem');//حذف محصول وکاهش تعداد محصول از سبد خرید
        Route::delete('/cart', [CartController::class, 'clear'])->middleware('checkCart:none'); // خالی کردن سبد خرید

        // حمل و نقل
        Route::get('/shipping/options', [ShippingController::class, 'index']);//نمایش روش‌های ارسال
        Route::get('/shipping/{shippingMethod}', [ShippingController::class, 'show']);//انتخاب و مشاهده روش ارسال

        // تسویه حساب
        Route::post('/checkout', [OrderController::class, 'checkout'])->middleware('checkCart:none');//مرحله نهایی کردن سفارش
        // سفارشات
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{order}', [OrderController::class, 'show'])->middleware('checkOrder');
        Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->middleware('checkOrder'); // لغو سفارش

        Route::get('pay', [PayController::class, 'choosePayWay']);
        Route::get('verifyPayment', [PayController::class, 'verifyPayment']);
        Route::get('invoices', [InvoiceController::class, 'index']);
    });


});


// احراز هویت
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
    Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'user']);
});

//پنل مدیریت فروشگاه
Route::prefix('admin')->middleware(['auth:sanctum', 'checkIsAdmin'])->group(function () {

    Route::apiResource('categories', AdminCategoryController::class);
    Route::apiResource('attributes', AttributeController::class);
    Route::apiResource('brands', AdminBrandController::class);
    Route::apiResource('categories/{category}/attributes', CategoryAttributeController::class)->only(['index', 'store', 'destroy']);

    Route::get('products/create', [AdminProductController::class, 'create']);
    Route::get('products/{product}/edit', [AdminProductController::class, 'edit']);
    Route::apiResource('products', AdminProductController::class);
    Route::post('setPrimaryImage/{product}/{productImage}', [AdminProductController::class, 'setPrimaryImage']);

});
