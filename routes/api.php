<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    UserController,
    FlightController,
    HotelController,
    CarController,
    BookingController,
    NotificationController,
    PaymentController,
    ReviewController,
    PassportController,
    SearchController,
    ProfileController,
    ExploreController
};

    // Public
    Route::prefix('public')->group(function () {
        Route::get('flights', [SearchController::class, 'searchFlights']);
        Route::get('hotels', [SearchController::class, 'searchHotels']);
        Route::get('cars', [SearchController::class, 'searchCars']);
        Route::get('explore', [ExploreController::class, 'index']);
    });


    // cars
    Route::prefix('cars')->group(function () {
        Route::get('/list', [CarController::class, 'index'])->name('cars.index');
        Route::get('/view/{car}', [CarController::class, 'show'])->name('cars.show');

        Route::middleware(['auth:sanctum'])->group(function () {
            Route::post('/create', [CarController::class, 'store'])->name('cars.store');
            Route::put('/update/{car}', [CarController::class, 'update'])->name('cars.update');
            Route::delete('/delete/{car}', [CarController::class, 'destroy'])->name('cars.destroy');
            Route::put('/toggle-availability/{car}', [CarController::class, 'toggleAvailability'])->name('cars.toggleAvailability');
        });
    });



    // flights
    Route::prefix('flights')->group(function () {
        Route::get('/list', [FlightController::class, 'index'])->name('flights.index');
        Route::get('/view/{flight}', [FlightController::class, 'show'])->name('flights.show');

        Route::middleware(['auth:sanctum'])->group(function () {
            Route::post('/create', [FlightController::class, 'store'])->name('flights.store');
            Route::put('/update/{flight}', [FlightController::class, 'update'])->name('flights.update');
            Route::delete('/delete/{flight}', [FlightController::class, 'destroy'])->name('flights.destroy');
            Route::put('/toggle-availability/{flight}', [FlightController::class, 'toggleAvailability'])->name('flights.toggleAvailability');
        });
    });


    // hotels
    Route::prefix('hotels')->group(function () {
        Route::get('/list', [HotelController::class, 'index'])->name('hotels.index');
        Route::get('/view/{hotel}', [HotelController::class, 'show'])->name('hotels.show');

        Route::middleware(['auth:sanctum'])->group(function () {
            Route::post('/create', [HotelController::class, 'store'])->name('hotels.store');
            Route::put('/update/{hotel}', [HotelController::class, 'update'])->name('hotels.update');
            Route::delete('/delete/{hotel}', [HotelController::class, 'destroy'])->name('hotels.destroy');
            Route::put('/toggle-availability/{hotel}', [HotelController::class, 'toggleAvailability'])->name('hotels.toggleAvailability');
        });
    });

    // Authentication
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
        Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

        Route::middleware(['auth:sanctum'])->group(function () {
            Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
        });
    });

    // Profile
    Route::prefix('profile')->group(function () {
        Route::middleware(['auth:sanctum'])->group(function () {
            Route::get('/view', [ProfileController::class, 'index'])->name('profile.index');
            Route::put('/update', [ProfileController::class, 'update'])->name('profile.update');
            Route::get('/favorites/list', [ProfileController::class, 'favorites'])->name('profile.favorites');
            Route::post('/favorites/add', [ProfileController::class, 'addFavorite'])->name('profile.addFavorite');
            Route::delete('/favorites/delete/{favorite}', [ProfileController::class, 'removeFavorite'])->name('profile.removeFavorite');
        });
    });

    // Users
    Route::prefix('users')->group(function () {
        Route::middleware(['auth:sanctum'])->group(function () {
            Route::get('/list', [UserController::class, 'index'])->name('users.index');
            Route::get('/view/{user}', [UserController::class, 'show'])->name('users.show');
            Route::post('/create', [UserController::class, 'store'])->name('users.store');
            Route::put('/update/{user}', [UserController::class, 'update'])->name('users.update');
            Route::delete('/delete/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        });
    });

    // Bookings
    Route::prefix('bookings')->group(function () {
        Route::middleware(['auth:sanctum'])->group(function () {
            Route::get('/list', [BookingController::class, 'index'])->name('bookings.index');
            Route::get('/view/{booking}', [BookingController::class, 'show'])->name('bookings.show');
            Route::post('/create', [BookingController::class, 'store'])->name('bookings.store');
            Route::put('/update/{booking}', [BookingController::class, 'update'])->name('bookings.update');
            Route::delete('/delete/{booking}', [BookingController::class, 'destroy'])->name('bookings.destroy');
            Route::put('/cancel/{booking}', [BookingController::class, 'cancel'])->name('bookings.cancel');
        });
    });

    // Payments
    Route::prefix('payments')->group(function () {
        Route::middleware(['auth:sanctum'])->group(function () {
            Route::get('/list', [PaymentController::class, 'index'])->name('payments.index');
            Route::get('/view/{payment}', [PaymentController::class, 'show'])->name('payments.show');
            Route::post('/create', [PaymentController::class, 'store'])->name('payments.store');
            Route::put('/update/{payment}', [PaymentController::class, 'update'])->name('payments.update');
            Route::delete('/delete/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
            Route::put('/mark-completed/{payment}', [PaymentController::class, 'markAsCompleted'])->name('payments.markAsCompleted');
        });
    });

    // Reviews
    Route::prefix('reviews')->group(function () {
        Route::middleware(['auth:sanctum'])->group(function () {
            Route::get('/list', [ReviewController::class, 'index'])->name('reviews.index');
            Route::get('/view/{review}', [ReviewController::class, 'show'])->name('reviews.show');
            Route::post('/create', [ReviewController::class, 'store'])->name('reviews.store');
            Route::put('/update/{review}', [ReviewController::class, 'update'])->name('reviews.update');
            Route::delete('/delete/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
            Route::put('/verify/{review}', [ReviewController::class, 'verify'])->name('reviews.verify')->middleware('role:admin');
        });
    });

    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::middleware(['auth:sanctum'])->group(function () {
            Route::get('/list', [NotificationController::class, 'index'])->name('notifications.index');
            Route::get('/view/{notification}', [NotificationController::class, 'show'])->name('notifications.show');
            Route::post('/create', [NotificationController::class, 'store'])->name('notifications.store');
            Route::put('/update/{notification}', [NotificationController::class, 'update'])->name('notifications.update');
            Route::delete('/delete/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
            Route::put('/mark-read/{notification}', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
            Route::put('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
            Route::get('/search', [NotificationController::class, 'search'])->name('notifications.search');
        });
    });

    // Passports
    Route::prefix('passports')->group(function () {
        Route::middleware(['auth:sanctum'])->group(function () {
            Route::get('/list', [PassportController::class, 'index'])->name('passports.index');
            Route::get('/view/{passport}', [PassportController::class, 'show'])->name('passports.show');
            Route::post('/create', [PassportController::class, 'store'])->name('passports.store');
            Route::put('/update/{passport}', [PassportController::class, 'update'])->name('passports.update');
            Route::delete('/delete/{passport}', [PassportController::class, 'destroy'])->name('passports.destroy');
            Route::put('/verify/{passport}', [PassportController::class, 'verify'])->name('passports.verify');
        });
    });

    // Test
    Route::get('/test', function () {
        return response()->json(['message' => 'API is working']);
    });
