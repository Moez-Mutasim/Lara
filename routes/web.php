<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HomeController,
    FlightController,
    HotelController,
    CarController,
    BookingController
};


Route::get('/home', [HomeController::class, 'index'])
    ->middleware(['auth'])
    ->name('home');

// Public
/*Route::resource('flights', FlightController::class)
    ->only(['index', 'show'])
    ->names([
        'index' => 'flights.index',
        'show' => 'flights.show',
    ]);

Route::resource('hotels', HotelController::class)
    ->only(['index', 'show'])
    ->names([
        'index' => 'hotels.index',
        'show' => 'hotels.show',
    ]);

Route::resource('cars', CarController::class)
    ->only(['index', 'show'])
    ->names([
        'index' => 'cars.index',
        'show' => 'cars.show',
    ]);

// Authenticated
Route::middleware(['auth'])->group(function () {
    Route::resource('bookings', BookingController::class)
        ->names([
            'index' => 'bookings.index',
            'create' => 'bookings.create',
            'store' => 'bookings.store',
            'show' => 'bookings.show',
            'edit' => 'bookings.edit',
            'update' => 'bookings.update',
            'destroy' => 'bookings.destroy',
        ]);
});*/
