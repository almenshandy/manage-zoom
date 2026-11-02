<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use App\Models\ZoomBooking;
use App\Observers\ZoomBookingObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ZoomBooking::observe(ZoomBookingObserver::class);

        // Paksa skema HTTPS jika diakses melalui tunneling/reverse proxy (ngrok, cloudflare, SSL)
        if (
            request()->isSecure() ||
            request()->header('x-forwarded-proto') === 'https' ||
            request()->header('x-forwarded-ssl') === 'on' ||
            str_contains(config('app.url'), 'https://')
        ) {
            URL::forceScheme('https');
        }
    }
}
