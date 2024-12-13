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
        return [
            'user_id' => User::factory(),
            'flight_id' => $this->faker->boolean(80) ? Flight::factory() : null,
            'hotel_id' => $this->faker->boolean(70) ? Hotel::factory() : null,
            'car_id' => $this->faker->boolean(50) ? Car::factory() : null,
            'booking_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'total_price' => function (array $attributes) {
                return ($attributes['flight_id'] ? Flight::find($attributes['flight_id'])->price : 0) +
                       ($attributes['hotel_id'] ? Hotel::find($attributes['hotel_id'])->price_per_night : 0) +
                       ($attributes['car_id'] ? Car::find($attributes['car_id'])->rental_price : 0);
            },
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'canceled']),
        ];
    }

   
    public function confirmed()
    {return $this->state(fn () => ['status' => 'confirmed']);}

    
    public function canceled()
    {return $this->state(fn () => ['status' => 'canceled']);}
}
