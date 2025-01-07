<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $authUser)
    {
        return $authUser->isAdmin();
    }

    public function view(User $authUser, User $user)
    {
        return $authUser->isAdmin() || $authUser->id === $user->id;
    }

    public function create(User $authUser)
    {
        return $authUser->isAdmin();
    }

    public function update(User $authUser, User $user)
    {
        return $authUser->isAdmin() || $authUser->id === $user->id;
    }

    public function delete(User $authUser, User $user)
    {
        return $authUser->isAdmin();
    }
}
