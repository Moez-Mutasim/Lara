<?php

namespace App\Policies;

use App\Models\Passport;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PassportPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any passports.
     */
    public function viewAny(User $user)
    {
        return $user->role === 'admin';
    }

    /**
     * Determine if the user can view the passport.
     */
    public function view(User $user, Passport $passport)
    {
        return $user->role === 'admin' || $user->id === $passport->user_id;
    }

    /**
     * Determine if the user can create a passport.
     */
    public function create(User $user)
    {
        return $user->role === 'admin';
    }

    /**
     * Determine if the user can update the passport.
     */
    public function update(User $user, Passport $passport)
    {
        return $user->role === 'admin' || $user->id === $passport->user_id;
    }

    /**
     * Determine if the user can delete the passport.
     */
    public function delete(User $user, Passport $passport)
    {
        return $user->role === 'admin';
    }

    /**
     * Determine if the user can verify the passport.
     */
    public function verify(User $user)
    {
        return $user->role === 'admin';
    }
}
