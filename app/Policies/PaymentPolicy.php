<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user)
    {
        return $user->isAdmin(); // Only admins can view payments
    }

    public function view(User $user, Payment $payment)
    {
        return $user->isAdmin() || $user->id === $payment->booking->user_id;
    }

    public function create(User $user)
    {
        return $user->isAdmin();
    }

    public function update(User $user, Payment $payment)
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Payment $payment)
    {
        return $user->isAdmin();
    }
}
