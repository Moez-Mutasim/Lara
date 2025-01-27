<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        \App\Models\User::class => \App\Policies\UserPolicy::class,
        \App\Models\Booking::class => \App\Policies\BookingPolicy::class,
        \App\Models\Flight::class => \App\Policies\FlightPolicy::class,
        \App\Models\Hotel::class => \App\Policies\HotelPolicy::class,
        \App\Models\Car::class => \App\Policies\CarPolicy::class,
        \App\Models\Review::class => \App\Policies\ReviewPolicy::class,
        \App\Models\Payment::class => \App\Policies\PaymentPolicy::class,
        \App\Models\Notification::class => \App\Policies\NotificationPolicy::class,
        \App\Models\Passport::class => \App\Policies\PassportPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();


        Gate::define('viewExplore', function ($user) {
            return $user && $user->isAuthenticated();
        });


        Gate::define('viewHome', function ($user) {
            Gate::define('viewHome', [HomePolicy::class, 'viewHome']);
        });


        Gate::define('searchFlights', function ($user) {
            return true;
        });

        Gate::define('searchHotels', function ($user) {
            return true;
        });

        Gate::define('searchCars', function ($user) {
            return true;
        });
    }
}
