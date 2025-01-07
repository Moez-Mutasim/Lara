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
        $flight = $this->faker->boolean(80) ? Flight::factory() : null;
        $hotel = $this->faker->boolean(70) ? Hotel::factory() : null;
        $car = $this->faker->boolean(50) ? Car::factory() : null;

        return [
            'user_id' => User::factory(),
            'flight_id' => $flight,
            'hotel_id' => $hotel,
            'car_id' => $car,
            'booking_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'total_price' => function () use ($flight, $hotel, $car) {
                return ($flight ? $flight->price : 0) +
                       ($hotel ? $hotel->price_per_night : 0) +
                       ($car ? $car->rental_price : 0);
            },
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'canceled']),
        ];
    }

    public function confirmed()
    {
        return $this->state(fn () => ['status' => 'confirmed']);
    }

    public function canceled()
    {
        return $this->state(fn () => ['status' => 'canceled']);
    }

    public function withFlightOnly()
    {
        return $this->state(fn () => ['flight_id' => Flight::factory(), 'hotel_id' => null, 'car_id' => null]);
    }

    public function withHotelOnly()
    {
        return $this->state(fn () => ['hotel_id' => Hotel::factory(), 'flight_id' => null, 'car_id' => null]);
    }

    public function withCarOnly()
    {
        return $this->state(fn () => ['car_id' => Car::factory(), 'flight_id' => null, 'hotel_id' => null]);
    }
}
