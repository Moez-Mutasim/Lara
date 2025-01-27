<?php

namespace App\Policies;

use App\Models\User;

class ExplorePolicy
{
    /**
     * Determine if the user can access the explore page.
     */
    public function viewExplore(User $user): bool
    {
        // Allow only authenticated users or users with specific roles
        return $user->isAuthenticated() || $user->role === 'customer';
    }
}
