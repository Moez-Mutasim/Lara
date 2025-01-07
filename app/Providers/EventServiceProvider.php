<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        'App\Events\UserRegistered' => [
            'App\Listeners\SendWelcomeEmail',
        ],
    ];

    public function boot()
    {
        parent::boot();

        // Example: Log an event
        Event::listen('user.login', function ($event) {
            \Log::info("User logged in: {$event->user->email}");
        });
    }
}
