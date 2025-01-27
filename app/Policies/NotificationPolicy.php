<?php

namespace App\Policies;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationPolicy
{
    public function viewAny(User $user)
    {
        Log::info("Checking if user can view any notifications", ['user_id' => $user->user_id]);
        return true;
    }

  
    public function view(User $user, Notification $notification)
    {
        $canView = $user->user_id === $notification->user_id || $user->isAdmin();
        Log::info("Checking if user can view notification", [
            'user_id' => $user->user_id,
            'notification_id' => $notification->notification_id,
            'result' => $canView ? 'Allowed' : 'Denied'
        ]);
        return $canView;
    }

  
    public function create(User $user)
    {
        $canCreate = $user->isAdmin();
        Log::info("Checking if user can create notification", [
            'user_id' => $user->user_id,
            'result' => $canCreate ? 'Allowed' : 'Denied'
        ]);
        return $canCreate;
    }

  
    public function update(User $user, Notification $notification)
    {
        $canUpdate = $user->user_id === $notification->user_id  || $user->isAdmin();
        Log::info("Checking if user can update notification", [
            'user_id' => $user->user_id,
            'notification_id' => $notification->notification_id,
            'result' => $canUpdate ? 'Allowed' : 'Denied'
        ]);
        return $canUpdate;
    }

  
    public function delete(User $user, Notification $notification)
    {
        $canDelete = $user->user_id === $notification->user_id || $user->isAdmin();
        Log::info("Checking if user can delete notification", [
            'user_id' => $user->user_id,
            'notification_id' => $notification->notification_id,
            'result' => $canDelete ? 'Allowed' : 'Denied'
        ]);
        return $canDelete;
    }

  
    public function markAllAsRead(?User $user)
    {
        Log::info("Checking if user can mark all notifications as read", ['user_id' => $user->user_id]);
        return true;
    }

    public function markAsRead(?User $user)
    {
        Log::info("Checking if user can mark a notifications as read", ['user_id' => $user->user_id]);
        return true;
    }
}
