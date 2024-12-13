<?php

namespace Database\Factories;

use App\Models\Flight;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

class FlightFactory extends Factory
{
    protected $model = Flight::class;

    public function definition()
    {
        $departureLocation = Location::inRandomOrder()->first() ?? Location::factory()->create();
        $destinationLocation = Location::inRandomOrder()->first() ?? Location::factory()->create();

        while ($destinationLocation->location_id === $departureLocation->location_id) {
            $destinationLocation = Location::factory()->create();
        }

        $departureTime = $this->faker->dateTimeBetween('-1 year', 'now');
        $arrivalTime = $this->faker->dateTimeBetween($departureTime, '+12 hours');

        $duration = $arrivalTime->diff($departureTime)->format('%h hours %i minutes');

        return [
            'airline_name' => $this->faker->randomElement(['Delta Airlines', 'American Airlines', 'United Airlines', 'Emirates', 'Lufthansa']),
            'departure_id' => $departureLocation->location_id,
            'destination_id' => $destinationLocation->location_id,
            'departure_time' => $departureTime,
            'arrival_time' => $arrivalTime,
            'duration' => $duration,
            'price' => $this->faker->randomFloat(2, 100, 1000),
            'seats_available' => $this->faker->numberBetween(50, 200),
            'class' => $this->faker->randomElement(['Economy', 'Business', 'First']),
        ];
    }

    
    public function economyClass()
    {return $this->state(fn () => ['class' => 'Economy']);}

   
    public function fullyBooked()
    {return $this->state(fn () => ['seats_available' => 0]);}
}
