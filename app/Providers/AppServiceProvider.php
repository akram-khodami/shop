<?php

namespace App\Providers;

use App\Services\PayServiceInterface;
use App\Services\ZarinpalPayService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PayServiceInterface::class, ZarinpalPayService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
