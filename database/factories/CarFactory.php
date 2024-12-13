<?php

namespace Database\Factories;

use App\Models\Car;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarFactory extends Factory
{
    protected $model = Car::class;

    public function definition()
    {
        return [
            'brand' => $this->faker->company(),
            'model' => $this->faker->year(),
            'rental_price' => $this->faker->randomFloat(2, 30, 300),
            'availability' => $this->faker->boolean(80),
            'features' => json_encode($this->faker->randomElements([
                'GPS', 'Air Conditioning', 'Heated Seats', 'Bluetooth', 'Cruise Control',
            ], $this->faker->numberBetween(1, 3))),
        ];
    }

    
    public function luxury()
    {
        return $this->state(fn () => [
            'rental_price' => $this->faker->randomFloat(2, 200, 500),
            'features' => json_encode(['Leather Seats', 'Premium Sound System', 'Sunroof']),
        ]);
    }

    
    public function unavailable()
    {return $this->state(fn () => ['availability' => false]);}
}
