<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function viewAny(User $user)
    {
        return true; // Allow all authenticated users to view bookings
    }

    public function view(User $user, Booking $booking)
    {
        return $user->isAdmin() || $user->id === $booking->user_id;
    }

    public function create(User $user)
    {
        return true; // Any user can create a booking
    }

    public function update(User $user, Booking $booking)
    {
        return $user->id === $booking->user_id || $user->isAdmin();
    }

    public function delete(User $user, Booking $booking)
    {
        return $user->isAdmin();
    }
}
