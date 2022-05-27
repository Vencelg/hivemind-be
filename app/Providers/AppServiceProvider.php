<?php

namespace App\Providers;

use http\Client\Request;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        RateLimiter::for('resend', function (\Illuminate\Http\Request $request) {
            return Limit::perMinutes(3, 1)->by($request->user()->id);
        });
    }
}
