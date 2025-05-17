<?php

use App\Exceptions\Handler;
use App\Http\Middleware\CheckCartItem;
use App\Http\Middleware\CheckCartNotEmpty;
use App\Http\Middleware\CheckOrder;
use App\Http\Middleware\CheckStockAvailable;
use App\Http\Middleware\IsAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
    web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
    //
    $middleware->alias(
        [
            'checkCart' => CheckCartNotEmpty::class,
            'checkStock' => CheckStockAvailable::class,
            'checkCartItem' => CheckCartItem::class,
            'checkIsAdmin' => IsAdmin::class,
            'checkOrder' => CheckOrder::class,
        ]);
})
    ->withExceptions(function (Exceptions $exceptions) {

        // مدیریت خطاهای API
        $exceptions->renderable(function (Throwable $e, $request) {
            if ($request->expectsJson()) {
                $status = method_exists($e, 'getStatusCode')
                    ? $e->getStatusCode()
                    : 500;

                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'status' => $status,
                    'trace' => config('app.debug') && $e ? $e->getTrace() : null,
                ], $status);
            }
        });

        // لاگ کردن خطاهای خاص
        $exceptions->reportable(function (Throwable $e) {
            if (app()->environment('production')) {
                // ارسال به سرویس مانیتورینگ خطا
            }
        });
    })->create();
