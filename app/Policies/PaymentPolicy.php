<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    
    public function viewAny(?User $user)
    {
        return $user->isAdmin();
    }

    
    public function view(User $user, Payment $payment)
    {
        return $user->isAdmin() || 
               ($payment->booking && $user->user_id === $payment->booking->user_id);
    }

    
    public function create(?User $user)
    {
        return $user->isAdmin() || $user->isCustomer();
    }

   
    public function update(User $user, Payment $payment)
    {
        return $user->isAdmin() || 
               ($user->user_id === $payment->booking->user_id && $payment->payment_status === 'pending');
    }


    public function delete(User $user, Payment $payment)
    {
        return $user->isAdmin();
    }

   
    public function markAsCompleted(User $user, Payment $payment)
    {
        return $user->isAdmin();
    }
}
