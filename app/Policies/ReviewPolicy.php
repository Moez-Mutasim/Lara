<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    public function viewAny(User $user)
    {
        return true; // Allow all users to view reviews
    }

    public function view(User $user, Review $review)
    {
        return $user->id === $review->user_id || $user->isAdmin();
    }

    public function create(User $user)
    {
        return true; // Any user can create a review
    }

    public function update(User $user, Review $review)
    {
        return $user->id === $review->user_id || $user->isAdmin();
    }

    public function delete(User $user, Review $review)
    {
        return $user->isAdmin();
    }
}
