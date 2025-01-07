<?php

namespace Database\Factories;

use App\Models\Favorite;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FavoriteFactory extends Factory
{
    protected $model = Favorite::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'item_type' => $this->faker->randomElement(['flight', 'hotel', 'car']),
            'item_id' => $this->faker->randomNumber(5), // Random item ID
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
