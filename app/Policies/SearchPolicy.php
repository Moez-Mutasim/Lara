<?php

namespace App\Policies;

use App\Models\User;

class SearchPolicy
{
    /**
     * Determine if the user can search flights.
     */
    public function searchFlights(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can search hotels.
     */
    public function searchHotels(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can search cars.
     */
    public function searchCars(User $user): bool
    {
        return true;
    }
}
