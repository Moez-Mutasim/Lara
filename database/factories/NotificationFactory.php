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
            'user_id' => User::factory(), // Create or use a random user
            'type' => $this->faker->randomElement(['info', 'warning', 'error', 'success']),
            'message' => $this->faker->randomElement([
                'Your booking has been confirmed.',
                'Your payment was successful.',
                'Your booking has been canceled.',
                'New promotional offer available.',
                'Check-in reminder for your upcoming trip.',
                'Your account has been updated.',
            ]),
            'is_read' => false,
            'read_at' => null,
        ];
    }

    public function read()
    {
        return $this->state(fn () => [
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function unread()
    {
        return $this->state(fn () => [
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    public function forUser(User $user)
    {
        return $this->state(fn () => ['user_id' => $user->user_id]);
    }
}
