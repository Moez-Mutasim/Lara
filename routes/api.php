<?php

use App\Http\Controllers\Api\{
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


Route::group(['prefix' => 'public'], function () {
    Route::apiResource('flights', FlightController::class);
    Route::apiResource('hotels', HotelController::class)->only(['index', 'show']);
    Route::apiResource('cars', CarController::class)->only(['index', 'show']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('bookings', BookingController::class);
    Route::apiResource('payments', PaymentController::class);
    Route::apiResource('reviews', ReviewController::class);


    Route::apiResource('notifications', NotificationController::class)->except(['create', 'edit']);
    Route::put('notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);


    Route::get('flights/search', [FlightController::class, 'search']);
    Route::get('hotels/search', [HotelController::class, 'search']);
    Route::get('reviews/search', [ReviewController::class, 'search']);
    Route::get('notifications/search', [NotificationController::class, 'search']);
    Route::get('payments/search', [PaymentController::class, 'search']);
    Route::get('users/search', [UserController::class, 'search']);


    Route::middleware('role:admin')->group(function () {
        Route::post('admin-only-action', [AdminController::class, 'performAction']);
        Route::put('flights/{id}/toggle-availability', [FlightController::class, 'toggleAvailability']);
        Route::put('hotels/{id}/toggle-availability', [HotelController::class, 'toggleAvailability']);
        Route::put('cars/{id}/toggle-availability', [CarController::class, 'toggleAvailability']);
        Route::put('payments/{id}/mark-as-completed', [PaymentController::class, 'markAsCompleted']);
    });
});


Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});
