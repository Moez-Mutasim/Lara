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

        // Example: Custom gate for admin role
        Gate::define('admin-access', function ($user) {
            return $user->role === 'admin';
        });
    }
}
