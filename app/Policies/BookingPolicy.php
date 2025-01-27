<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->isCustomer();
    }

   
    public function view(User $user, Booking $booking)
    {
        return $user->isAdmin() || $user->user_id === $booking->user_id;
    }

   
    public function create(User $user)
    {
        return $user->isAdmin() || $user->isCustomer();
    }

   
    public function update(User $user, Booking $booking)
    {
        return $user->user_id === $booking->user_id || $user->isAdmin();
    }

 
    public function delete(User $user, Booking $booking)
    {
        return $user->user_id === $booking->user_id || $user->isAdmin();
    }

  
    public function cancel(User $user, Booking $booking)
    {
        return $user->user_id === $booking->user_id || $user->isAdmin();
    }


    public function before(User $user, $ability)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }
}
