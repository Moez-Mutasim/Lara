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
    AdminController
};

// Prefix all API routes with /api
//Route::prefix('api')->group(function () {

    // Public routes
    Route::prefix('public')->group(function () {
        Route::apiResource('flights', FlightController::class)->only(['index', 'show']);
        Route::apiResource('hotels', HotelController::class)->only(['index', 'show']);
        Route::apiResource('cars', CarController::class)->only(['index', 'show']);
    });

    // Authentication Routes
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // Authenticated Routes
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::apiResource('users', UserController::class);
        Route::get('users/search', [UserController::class, 'search']);
        Route::post('logout', [AuthController::class, 'logout']);

        Route::apiResource('bookings', BookingController::class);

        Route::apiResource('payments', PaymentController::class);
        Route::get('payments/search', [PaymentController::class, 'search']);
        Route::put('payments/{id}/mark-as-completed', [PaymentController::class, 'markAsCompleted']);

        Route::apiResource('reviews', ReviewController::class);
        Route::get('reviews/search', [ReviewController::class, 'search']);

        Route::apiResource('notifications', NotificationController::class)->except(['create', 'edit']);
        Route::put('notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
        Route::get('notifications/search', [NotificationController::class, 'search']);

        // Private flight actions
        Route::get('flights/search', [FlightController::class, 'search']);
        Route::put('flights/{id}/toggle-availability', [FlightController::class, 'toggleAvailability']);

        // Admin-only actions
        Route::middleware(['role:admin'])->group(function () {
            //Route::post('admin-only-action', [AdminController::class, 'performAction']);
            Route::put('hotels/{id}/toggle-availability', [HotelController::class, 'toggleAvailability']);
            Route::put('cars/{id}/toggle-availability', [CarController::class, 'toggleAvailability']);
        });
    });

    // Test endpoint
    Route::get('/test', function () {
        return response()->json(['message' => 'API is working']);
    });
//});
