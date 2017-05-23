<?php

namespace App\Providers;

use App\Support\Services\Discount;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class DiscountServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Discount', function ($app) {
            $service = studly_case($this->app['config']->get('discount.service'));
            $service = $this->app->make("App\\Services\\$service");
            return new Discount($service);
        });
    }
}
