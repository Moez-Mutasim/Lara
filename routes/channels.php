<?php

use Illuminate\Support\Facades\Broadcast;

// Example channel
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
