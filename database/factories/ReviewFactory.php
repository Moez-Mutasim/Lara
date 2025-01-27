<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\User;
use App\Models\Flight;
use App\Models\Car;
use App\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->value('user_id') ?? User::factory(),
            'flight_id' => null,
            'car_id' => null,
            'hotel_id' => null,
            'rating' => $this->faker->randomFloat(1, 1, 5),
            'comment' => $this->faker->optional()->randomElement([
                'Great experience!',
                'Room service was excellent.',
                'The car was clean and well-maintained.',
                'Flight was delayed but service was good.',
                'The staff was very friendly and helpful.',
                'Would not recommend, had a bad experience.',
            ]),
            'is_verified' => $this->faker->boolean(70),
        ];
    }

    public function forUser(User $user)
    {
        return $this->state(fn () => ['user_id' => $user->user_id]);
    }

    public function forFlight(Flight $flight)
    {
        return $this->state(fn () => [
            'flight_id' => $flight->flight_id,
            'car_id' => null,
            'hotel_id' => null
        ]);
    }

    public function forCar(Car $car)
    {
        return $this->state(fn () => [
            'car_id' => $car->car_id,
            'flight_id' => null,
            'hotel_id' => null
        ]);
    }

    public function forHotel(Hotel $hotel)
    {
        return $this->state(fn () => [
            'hotel_id' => $hotel->hotel_id,
            'flight_id' => null,
            'car_id' => null
        ]);
    }

    public function forFlightOnly()
    {
        return $this->state(fn () => [
            'flight_id' => Flight::inRandomOrder()->value('flight_id') ?? Flight::factory(),
            'car_id' => null,
            'hotel_id' => null,
        ]);
    }

    public function forHotelOnly()
    {
        return $this->state(fn () => [
            'hotel_id' => Hotel::inRandomOrder()->value('hotel_id') ?? Hotel::factory(),
            'flight_id' => null,
            'car_id' => null,
        ]);
    }

    public function forCarOnly()
    {
        return $this->state(fn () => [
            'car_id' => Car::inRandomOrder()->value('car_id') ?? Car::factory(),
            'flight_id' => null,
            'hotel_id' => null,
        ]);
    }

    public function verified()
    {
        return $this->state(fn () => ['is_verified' => true]);
    }

    public function unverified()
    {
        return $this->state(fn () => ['is_verified' => false]);
    }
}
