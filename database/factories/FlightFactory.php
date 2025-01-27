<?php

namespace Database\Factories;

use App\Models\Flight;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

class FlightFactory extends Factory
{
    protected $model = Flight::class;

    public function definition()
    {
        $departureLocation = Location::inRandomOrder()->first() ?? Location::factory()->create();
        do {
            $destinationLocation = Location::inRandomOrder()->first() ?? Location::factory()->create();
        } while ($destinationLocation->location_id === $departureLocation->location_id);

        $departureTime = $this->faker->dateTimeBetween('now', '+1 year');
        $arrivalTime = (clone $departureTime)->modify('+'.rand(1, 12).' hours');
        $duration = $arrivalTime->diff($departureTime)->format('%h hours %i minutes');

        return [
            'airline_name' => $this->faker->randomElement([
                'Qatar Airlines', 'American Airlines', 'Etihad Airlines', 'Emirates Airlines', 
                'Lufthansa Airlines', 'Saudi Airlines', 'Gulf Airlines'
            ]),
            'departure_id' => $departureLocation->location_id,
            'destination_id' => $destinationLocation->location_id,
            'departure_time' => $departureTime,
            'arrival_time' => $arrivalTime,
            'duration' => $duration,
            'price' => $this->faker->randomFloat(2, 100, 1000),
            'seats_available' => $this->faker->numberBetween(50, 200),
            'class' => $this->faker->randomElement(['Economy', 'Business', 'First']),
            'is_available' => $this->faker->boolean(80),
            'flight_image' => $this->generateImage(),
        ];
    }

   
    public function economyClass()
    {
        return $this->state(fn () => ['class' => 'Economy']);
    }

  
    public function fullyBooked()
    {
        return $this->state(fn () => ['seats_available' => 0]);
    }

  
    private function generateImage()
    {
        $path = public_path('storage/flight_images');
            if (!Storage::disk('public')->exists('flight_images')) {
            Storage::disk('public')->makeDirectory('flight_images');
            }


        return $this->faker->optional()->imageUrl(640, 480, 'flights', true, 'Flight') ??
        Storage::disk('public')->putFile('flight_images', base_path('resources/placeholders/flights/f1.jpg'));
    }
}
