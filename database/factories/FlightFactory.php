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

        $departureTime = $this->faker->dateTimeBetween('now', '+1 year');
        $arrivalTime = $this->faker->dateTimeBetween($departureTime, '+12 hours');
        $duration = $arrivalTime->diff($departureTime)->format('%h hours %i minutes');

        return [
            'airline_name' => $this->faker->randomElement(['Qatar Airlines', 'American Airlines', 'Itihad Airlines', 'Emirates Airlines', 'Lufthansa Airlines', 'Saudi Airlines', 'Gulf Airlines']),
            'departure_id' => $departureLocation->location_id,
            'destination_id' => $destinationLocation->location_id,
            'departure_time' => $departureTime,
            'arrival_time' => $arrivalTime,
            'duration' => $duration,
            'price' => $this->faker->randomFloat(2, 100, 1000),
            'seats_available' => $this->faker->numberBetween(50, 200),
            'class' => $this->faker->randomElement(['Economy', 'Business', 'First']),
            'image' => $this->generateImage(),
        ];
    }

    
    public function economyClass()
    {return $this->state(fn () => ['class' => 'Economy']);}

   
    public function fullyBooked()
    {return $this->state(fn () => ['seats_available' => 0]);}

    private function generateImage()
    {
        $imageUrl = $this->faker->optional()->imageUrl(640, 480, 'flights', true, 'Flight');
       // return $this->faker->optional()->imageUrl(640, 480, 'flights', true, 'Flight');
    
        
            $path = public_path('storage/flight_images');
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
    
            return $this->faker->optional()->file(
                base_path('resources/placeholders/flights'),
                $path, // destination directory
                false // save just the file name
            ) ?: $imageUrl;
    
    
    }
}
