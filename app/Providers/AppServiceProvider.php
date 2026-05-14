<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\UrlGenerator;

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
    public function boot(UrlGenerator $urlGenerator): void
    {
        Paginator::useBootstrapFive();

        if (env('APP_ENV') === 'production') {
            $urlGenerator->forceScheme('https');
        }
    }
}
