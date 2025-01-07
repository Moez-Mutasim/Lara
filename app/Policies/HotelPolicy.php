<?php

namespace App\Policies;

use App\Models\Hotel;
use App\Models\User;

class HotelPolicy
{
    public function viewAny(User $user)
    {
        return true; // Allow all users to view hotels
    }

    public function view(User $user, Hotel $hotel)
    {
        return true; // All users can view hotel details
    }

    public function create(User $user)
    {
        return $user->isAdmin();
    }

    public function update(User $user, Hotel $hotel)
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Hotel $hotel)
    {
        return $user->isAdmin();
    }
}
