<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\User;
use App\Models\Flight;
use App\Models\Hotel;
use App\Models\Car;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition()
    {
        $flight_id = Flight::inRandomOrder()->value('flight_id');
        $hotel_id = Hotel::inRandomOrder()->value('hotel_id');
        $car_id = Car::inRandomOrder()->value('car_id');

        $hasFlight = $this->faker->boolean(80);
        $hasHotel = $this->faker->boolean(70);
        $hasCar = $this->faker->boolean(50);

        return [
            'user_id' => User::inRandomOrder()->value('user_id') ?? User::factory(),
            'flight_id' => $hasFlight ? $flight_id : null,
            'hotel_id' => $hasHotel ? $hotel_id : null,
            'car_id' => $hasCar ? $car_id : null,
            'booking_date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'total_price' => function () use ($hasFlight, $flight_id, $hasHotel, $hotel_id, $hasCar, $car_id) {
                return ($hasFlight && $flight_id ? Flight::find($flight_id)->price : 0) +
                       ($hasHotel && $hotel_id ? Hotel::find($hotel_id)->price_per_night : 0) +
                       ($hasCar && $car_id ? Car::find($car_id)->rental_price : 0);
            },
            'status' => $this->faker->randomElement(['pending', 'active', 'canceled']),
        ];
    }

   
    public function active()
    {
        return $this->state(fn () => ['status' => 'active']);
    }

   
    public function canceled()
    {
        return $this->state(fn () => ['status' => 'canceled']);
    }

   
    public function withFlightOnly()
    {
        return $this->state(fn () => [
            'flight_id' => Flight::inRandomOrder()->value('flight_id'),
            'hotel_id' => null,
            'car_id' => null,
        ]);
    }

   
    public function withHotelOnly()
    {
        return $this->state(fn () => [
            'hotel_id' => Hotel::inRandomOrder()->value('hotel_id'),
            'flight_id' => null,
            'car_id' => null,
        ]);
    }

   
    public function withCarOnly()
    {
        return $this->state(fn () => [
            'car_id' => Car::inRandomOrder()->value('car_id'),
            'flight_id' => null,
            'hotel_id' => null,
        ]);
    }
}
