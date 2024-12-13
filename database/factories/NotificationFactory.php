<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'message' => $this->faker->randomElement([
                'Your booking has been confirmed.',
                'Your payment was successful.',
                'Your booking has been canceled.',
                'New promotional offer available.',
                'Check-in reminder for your upcoming trip.',
            ]),
            'is_read' => false,
        ];
    }

    
    public function read()
    {return $this->state(fn () => ['is_read' => true]);}

    
    public function unread()
    {return $this->state(fn () => ['is_read' => false]);}

    
    public function forUser(User $user)
    {return $this->state(fn () => ['user_id' => $user->id]);}
}
