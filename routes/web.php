<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Api\FlightController;
use App\Http\Controllers\Api\HotelController;
use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\BookingController;

// Public routes
Route::get('/home', [HomeController::class, 'index'])->middleware(['auth', 'role:user,admin'])->name('home');
Route::get('/admin/dashboard', [DashboardController::class, 'index'])->middleware('auth:admin');


// Public resources (accessible without auth)
Route::resource('bookings', BookingController::class);
Route::resource('flights', FlightController::class)->only(['index', 'show']);
Route::resource('hotels', HotelController::class)->only(['index', 'show']);
Route::resource('cars', CarController::class)->only(['index', 'show']);

