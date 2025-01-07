<?php

namespace App\Policies;

use App\Models\Car;
use App\Models\User;

class CarPolicy
{
    public function viewAny(User $user)
    {
        return true; // Allow all users to view cars
    }

    public function view(User $user, Car $car)
    {
        return true; // All users can view car details
    }

    public function create(User $user)
    {
        return $user->isAdmin();
    }

    public function update(User $user, Car $car)
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Car $car)
    {
        return $user->isAdmin();
    }
}
