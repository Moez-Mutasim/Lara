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

// Public Routes
Route::prefix('public')->group(function () {
    Route::get('flights/search', [SearchController::class, 'searchFlights']);
    Route::get('hotels/search', [SearchController::class, 'searchHotels']);
    Route::get('cars/search', [SearchController::class, 'searchCars']);
    Route::get('explore', [ExploreController::class, 'index']); // Explore page
});

// Authentication Routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Authenticated Routes
Route::middleware(['auth:sanctum'])->group(function () {
    // User Profile
    Route::get('profile', [ProfileController::class, 'index']);
    Route::put('profile', [ProfileController::class, 'update']);
    Route::post('logout', [AuthController::class, 'logout']);

    // Favorites
    Route::get('favorites', [ProfileController::class, 'favorites']);
    Route::post('favorites', [ProfileController::class, 'addFavorite']);
    Route::delete('favorites/{id}', [ProfileController::class, 'removeFavorite']);

    // Bookings
    Route::apiResource('bookings', BookingController::class);
    Route::put('bookings/{id}/cancel', [BookingController::class, 'cancel']); // Cancel a booking

    // Payments
    Route::apiResource('payments', PaymentController::class);
    Route::put('payments/{id}/mark-completed', [PaymentController::class, 'markAsCompleted']);

    // Reviews
    Route::apiResource('reviews', ReviewController::class);

    // Notifications
    Route::apiResource('notifications', NotificationController::class)->only(['index', 'show', 'update']);
    Route::put('notifications/{id}/mark-read', [NotificationController::class, 'markAsRead']);

    // Passports
    Route::apiResource('passports', PassportController::class);

    // Admin-only Routes
    Route::middleware(['role:admin'])->group(function () {
        Route::put('flights/{id}/toggle-availability', [FlightController::class, 'toggleAvailability']);
        Route::put('hotels/{id}/toggle-availability', [HotelController::class, 'toggleAvailability']);
        Route::put('cars/{id}/toggle-availability', [CarController::class, 'toggleAvailability']);
    });
});

// Test Endpoint
Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});
