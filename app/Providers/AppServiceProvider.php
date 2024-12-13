<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    
    public function register()
    {
        $this->app->singleton('SomeService', function ($app) {
            return new \App\Services\SomeService();
        });
    }

    
    public function boot()
    {
        Schema::defaultStringLength(191);
        View::share('appName', config('app.name'));
        View::composer('*', function ($view) {
            $view->with('sharedVariable', 'This is shared across all views');
        });
    }
}
