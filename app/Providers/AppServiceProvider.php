<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        // Register services, bindings, and other singletons.
        $this->app->singleton('SomeService', function ($app) {
            return new \App\Services\SomeService();
        });

        // Add additional service registrations here as needed.
        $this->app->singleton(\App\Services\NotificationService::class, function () {
            return new \App\Services\NotificationService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {

        //parent::boot();
        $this->app['router']->aliasMiddleware('role', \App\Http\Middleware\RoleMiddleware::class);

        // Set default string length for MySQL compatibility.
        Schema::defaultStringLength(191);

        // Share global variables across all views.
        View::share('appName', config('app.name'));
        View::composer('*', function ($view) {
            $view->with('sharedVariable', 'Shared globally across views');
        });

        // Add API route mapping for cleaner routing.
        $this->mapApiRoutes();

        // Additional bootstrapping logic for multi-currency or multi-language.
        View::share('supportedCurrencies', config('app.supported_currencies', ['USD', 'EUR', 'SAR']));
        View::share('supportedLanguages', config('app.supported_languages', ['en', 'ar']));
    }

    /**
     * Define the "api" routes for the application.
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->app->getNamespace() . 'Http\Controllers\Api')
            ->group(base_path('routes/api.php'));
    }
}
