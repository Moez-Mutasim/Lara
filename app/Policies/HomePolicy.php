<?php

namespace App\Policies;

use App\Models\User;

class HomePolicy
{
    /**
     * Determine if the user can access the home page.
     */
    public function viewHome(User $user): bool
    {
        // Allow all authenticated users to view the home page
        //return $user->isAuthenticated();
        return true;
    }
}
