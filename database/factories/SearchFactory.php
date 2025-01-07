<?php

namespace Database\Factories;

use App\Models\Search;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SearchFactory extends Factory
{
    protected $model = Search::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'search_type' => $this->faker->randomElement(['flight', 'hotel', 'car']),
            'search_details' => json_encode([
                'location' => $this->faker->city,
                'date' => $this->faker->date,
                'passengers' => $this->faker->numberBetween(1, 5),
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
