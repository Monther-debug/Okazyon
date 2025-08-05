<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dev/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            $this->registerGeneralRoutes();
            $this->registerAdminRoutes();
            $this->registerUserRoutes();

            // Add other route registrations here if needed
            $this->registerWebRoutes();
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(100)->by($request->user()->id)
                : Limit::perMinute(30)->by($request->ip());
        });

        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(10)->by($request['phone_number']);
        });

        RateLimiter::for('check-phone-number', function (Request $request) {
            return Limit::perMinute(20)->by($request->ip());
        });

        RateLimiter::for('otp', function (Request $request) {
            return Limit::perMinute(5)->by($request['phone_number']);
        });
        RateLimiter::for('reset-password', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });
    }

    private function registerGeneralRoutes()
    {
        Route::middleware('api')
            ->prefix('api/v1/general')
            ->name('general.')
            ->group(base_path('routes/api/generalApi.php'));
    }

    private function registerAdminRoutes()
    {
        Route::middleware('api')
            ->prefix('api/v1/admin')
            ->name('admin.')
            ->group(base_path('routes/api/adminApi.php'));
    }

    private function registerUserRoutes()
    {
        Route::middleware('api')
            ->prefix('api/v1')
            ->name('user.')
            ->group(base_path('routes/api/userApi.php'));
    }

    private function registerWebRoutes()
    {
        Route::middleware('web')
            ->group(base_path('routes/web.php'));
    }
}
