<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HomeController,
    FlightController,
    HotelController,
    CarController,
    BookingController,
    ProfileController,
    SearchController,
    NotificationController,
    ExploreController
};
/*
// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Public Routes
Route::prefix('flights')->group(function () {
    Route::get('/', [FlightController::class, 'index'])->name('flights.index');
    Route::get('/{id}', [FlightController::class, 'show'])->name('flights.show');
});
Route::prefix('hotels')->group(function () {
    Route::get('/', [HotelController::class, 'index'])->name('hotels.index');
    Route::get('/{id}', [HotelController::class, 'show'])->name('hotels.show');
});
Route::prefix('cars')->group(function () {
    Route::get('/', [CarController::class, 'index'])->name('cars.index');
    Route::get('/{id}', [CarController::class, 'show'])->name('cars.show');
});

// Search Routes
Route::prefix('search')->group(function () {
    Route::get('/flights', [SearchController::class, 'searchFlights'])->name('search.flights');
    Route::get('/hotels', [SearchController::class, 'searchHotels'])->name('search.hotels');
    Route::get('/cars', [SearchController::class, 'searchCars'])->name('search.cars');
});

// Explore
Route::get('/explore', [ExploreController::class, 'index'])->name('explore');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // Profile and Favorites
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('profile.index');
        Route::put('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::get('/favorites', [ProfileController::class, 'favorites'])->name('profile.favorites');
        Route::post('/favorites', [ProfileController::class, 'addFavorite'])->name('profile.favorites.add');
        Route::delete('/favorites/{id}', [ProfileController::class, 'removeFavorite'])->name('profile.favorites.remove');
    });

    // Notifications
    Route::prefix('notifications')->group(function () {
      //  Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
        Route::put('/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.markRead');
    });

    // Booking Operations
    Route::prefix('bookings')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('bookings.index');
        Route::get('/{id}', [BookingController::class, 'show'])->name('bookings.show');
        Route::post('/', [BookingController::class, 'store'])->name('bookings.store');
        Route::put('/{id}', [BookingController::class, 'update'])->name('bookings.update');
        Route::delete('/{id}', [BookingController::class, 'destroy'])->name('bookings.destroy');
    });
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::resource('flights', FlightController::class)->except(['index', 'show']);
    Route::resource('hotels', HotelController::class)->except(['index', 'show']);
    Route::resource('cars', CarController::class)->except(['index', 'show']);
    Route::resource('bookings', BookingController::class)->except(['index', 'show']);
});

// Additional Authenticated Home Route
Route::get('/home', [HomeController::class, 'index'])->middleware('auth')->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
*/