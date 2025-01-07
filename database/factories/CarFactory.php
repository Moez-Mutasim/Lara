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
                'GPS', 'Air Conditioning', 'Heated Seats', 'Bluetooth', 'Cruise Control', 'Backup Camera',
            ], $this->faker->numberBetween(1, 5))),
            'image' => $this->generateImage(),
        ];
    }

    public function luxury()
    {
        return $this->state(fn () => [
            'rental_price' => $this->faker->randomFloat(2, 200, 500),
            'features' => json_encode([
                'Leather Seats', 'Premium Sound System', 'Sunroof', 'Advanced Safety Features',
            ]),
            'image' => $this->generateImage(),
        ]);
    }

    public function unavailable()
    {
        return $this->state(fn () => ['availability' => false]);
    }

    private function generateImage()
    {
        // Use Faker to generate a random image URL
        $imageUrl = $this->faker->optional()->imageUrl(640, 480, 'cars', true, 'Car');

        // Alternatively, generate local placeholder paths for local testing
        $path = public_path('storage/car_images');
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        return $this->faker->optional()->file(
            base_path('resources/placeholders/cars'),
            $path, // destination directory
            false // save just the file name
        ) ?: $imageUrl;
    }
}
