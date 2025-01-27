<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
   
    public function viewAny(?User $user)
    {
        return true;
    }


    public function view(User $user, Review $review)
    {
        return $user->user_id === $review->user_id || $user->isAdmin();
    }

  
    public function create(?User $user)
    {
        return $user->isAdmin() || $user->isCustomer();
    }

 
    public function update(User $user, Review $review)
    {
        return ($user->user_id === $review->user_id || $user->isAdmin()) && !$review->is_verified;
    }

  
    public function delete(User $user, Review $review)
    {
        return $user->isAdmin();
    }

   
    public function verify(?User $user)
    {
        return $user->isAdmin();
    }
}
