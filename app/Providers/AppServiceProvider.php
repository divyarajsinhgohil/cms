<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\SiteSetting;

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
        Paginator::useBootstrapFive();

        // Brute-force protection: 5 login attempts per minute for each login + IP pair,
        // and 20 per minute per IP overall (stops one IP spraying many accounts).
        RateLimiter::for('login', function (Request $request) {
            $login = strtolower((string) $request->input('login'));

            return [
                Limit::perMinute(5)->by($login.'|'.$request->ip()),
                Limit::perMinute(20)->by($request->ip()),
            ];
        });

        // Guarded so artisan commands (composer install, migrate, etc.) still work
        // before the table exists or when the database is not reachable yet.
        try {
            if (Schema::hasTable('site_settings')) {
                View::share('siteSettings', SiteSetting::current());
            }
        } catch (\Throwable) {
            // No database yet - views fall back to their default site name.
        }
    }
}
