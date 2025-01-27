<?php

namespace App\Policies;

use App\Models\Flight;
use App\Models\User;

class FlightPolicy
{
    public function viewAny(?User $user)
    {
        return true;
    }

    public function view(?User $user, Flight $flight)
    {
        return true;
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
