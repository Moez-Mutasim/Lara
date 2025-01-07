<?php

namespace App\Policies;

use App\Models\Notification;
use App\Models\User;

class NotificationPolicy
{
    public function viewAny(User $user)
    {
        return true; // All users can view their notifications
    }

    public function view(User $user, Notification $notification)
    {
        return $user->id === $notification->user_id || $user->isAdmin();
    }

    public function update(User $user, Notification $notification)
    {
        return $user->id === $notification->user_id;
    }
}
