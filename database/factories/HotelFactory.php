<?php

namespace Database\Factories;

use App\Models\Hotel;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

class HotelFactory extends Factory
{
    protected $model = Hotel::class;

    public function definition()
    {
        $city = Location::inRandomOrder()->first() ?? Location::factory()->create(['type' => 'city']);

        return [
            'name' => $this->faker->company() . ' Hotel',
            'city_id' => $city->location_id,
            'price_per_night' => $this->faker->randomFloat(2, 50, 500),
            'rating' => $this->faker->optional()->randomFloat(1, 1.0, 5.0),
            'amenities' => json_encode($this->faker->randomElements([
                'Free WiFi', 'Breakfast included', 'Swimming pool', 'Gym', 'Airport shuttle', 'Restaurant', 'Parking',
            ], $this->faker->numberBetween(2, 5))),
            'availability' => $this->faker->boolean(80),
        ];
    }

    
    public function luxury()
    {
        return $this->state(fn () => [
            'price_per_night' => $this->faker->randomFloat(2, 300, 1000),
            'rating' => $this->faker->randomFloat(1, 4.5, 5),
            'amenities' => json_encode(['Spa', 'Butler Service', 'Private Pool', 'Helipad']),
        ]);
    }

    
    public function unavailable()
    {return $this->state(fn () => ['availability' => false]);}
}
