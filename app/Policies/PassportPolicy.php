<?php

namespace App\Policies;

use App\Models\Passport;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PassportPolicy
{
    use HandlesAuthorization;

  
    public function viewAny(?User $user)
    {
        return $user->isAdmin();
    }

   
    public function view(User $user, Passport $passport)
    {
        return $user->isAdmin() || $user->user_id === $passport->user_id;
    }

    
    public function create(?User $user)
    {
        return $user->isAdmin();
    }

  
    public function update(User $user, Passport $passport)
    {
        return $user->isAdmin() || $user->user_id === $passport->user_id;
    }

   
    public function delete(User $user, Passport $passport)
    {
        return $user->isAdmin() || $user->user_id === $passport->user_id;
    }

   
    public function verify(?User $user)
    {
        return $user->isAdmin();
    }
}
