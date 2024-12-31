<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    UserController,
    FlightController,
    HotelController,
    CarController,
    BookingController,
    PaymentController,
    NotificationController,
    ReviewController,
    //AdminController
};

// Prefix all API routes with /api
//Route::prefix('api')->group(function () {

    // Public routes
    Route::prefix('public')->group(function () {
        Route::get('flights/search', [FlightController::class, 'search']);
        Route::get('hotels/search', [HotelController::class, 'search']);
        Route::get('cars/search', [CarController::class, 'search']);
    });

    // Authentication Routes
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // Authenticated Routes
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::apiResource('users', UserController::class);
        Route::get('users/search', [UserController::class, 'search'])->name('users.search');
        Route::get('users/profile', [UserController::class, 'profile'])->name('profile');
        Route::post('logout', [AuthController::class, 'logout']);

        Route::apiResource('bookings', BookingController::class);

        Route::get('payments/search', [PaymentController::class, 'search']);
        Route::apiResource('payments', PaymentController::class);
        Route::put('payments/{id}/mark-as-completed', [PaymentController::class, 'markAsCompleted']);

        Route::apiResource('reviews', ReviewController::class);
        Route::get('reviews/search', [ReviewController::class, 'search']);

        Route::get('notifications/search', [NotificationController::class, 'search']);
        Route::apiResource('notifications', NotificationController::class)->except(['create', 'edit']);
        Route::put('notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);


        // Admin-only actions
        Route::middleware(['role:admin'])->group(function () {
            //Route::post('admin-only-action', [AdminController::class, 'performAction']);
            Route::put('flights/{id}/toggle-availability', [FlightController::class, 'toggleAvailability']);
            Route::put('hotels/{id}/toggle-availability', [HotelController::class, 'toggleAvailability']);
            Route::put('cars/{id}/toggle-availability', [CarController::class, 'toggleAvailability']);
        });
    });

    // Test
    Route::get('/test', function () {
        return response()->json(['message' => 'API is working']);
    });
//});
