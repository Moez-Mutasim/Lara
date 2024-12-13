<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition()
    {
        return [
            'name' => $this->faker->city(),
            'country' => $this->faker->country(),
            'state' => $this->faker->state(),
            'iata_code' => $this->faker->unique()->regexify('[A-Z]{3}'),
            'latitude' => $this->faker->latitude(-90, 90),
            'longitude' => $this->faker->longitude(-180, 180),
            'is_active' => $this->faker->boolean(80),
            'type' => $this->faker->randomElement(['city', 'airport', 'landmark']),
        ];
    }

    
    public function city()
    {
        return $this->state(fn () => [
            'type' => 'city',
            'iata_code' => null,
        ]);
    }

    
    public function airport()
    {
        return $this->state(fn () => [
            'type' => 'airport',
            'iata_code' => $this->faker->regexify('[A-Z]{3}'),
        ]);
    }

    
    public function inactive()
    {
        return $this->state(fn () => [
            'is_active' => false,
        ]);
    }
}
