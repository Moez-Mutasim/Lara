<?php

namespace App\Policies;

use App\Models\Flight;
use App\Models\User;

class FlightPolicy
{
    public function viewAny(User $user)
    {
        return true; // Allow all authenticated users to view flights
    }

    public function view(User $user, Flight $flight)
    {
        return true; // All users can view flight details
    }

    public function create(User $user)
    {
        return $user->isAdmin();
    }

    public function update(User $user, Flight $flight)
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Flight $flight)
    {
        return $user->isAdmin();
    }
}
