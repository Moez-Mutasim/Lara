<?php

namespace Database\Factories;

use App\Models\Hotel;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

class HotelFactory extends Factory
{
    protected $model = Hotel::class;

    public function definition()
    {
        $city = Location::inRandomOrder()->first() ?? Location::factory()->create(['type' => 'city']);

        return [
            'name' => $this->faker->company() . ' Hotel',
            'city_id' => $city->location_id,
            'price_per_night' => $this->faker->randomFloat(2, 50, 500),
            'rating' => $this->faker->optional()->randomFloat(1, 1.0, 5.0),
            'amenities' => json_encode($this->faker->randomElements([
                'Free WiFi', 'Breakfast included', 'Swimming pool', 'Gym', 'Airport shuttle', 'Restaurant', 'Parking',
            ], $this->faker->numberBetween(2, 5))),
            'image' => $this->generateImage(),
            'availability' => $this->faker->boolean(80),
            'rooms_available' => $this->faker->numberBetween(0, 100),
        ];
    }

    public function luxury()
    {
        return $this->state(fn () => [
            'price_per_night' => $this->faker->randomFloat(2, 300, 1000),
            'rating' => $this->faker->randomFloat(1, 4.5, 5),
            'amenities' => json_encode(['Spa', 'Butler Service', 'Private Pool', 'Helipad']),
            'image' => $this->generateImage(),
            'rooms_available' => $this->faker->numberBetween(1, 50),
        ]);
    }

    public function unavailable()
    {
        return $this->state(fn () => ['availability' => false, 'rooms_available' => 0]);
    }

    private function generateImage()
    {
        $imageUrl = $this->faker->optional()->imageUrl(640, 480, 'hotel', true, 'Hotel');

        $path = public_path('storage/hotel_images');
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        return $this->faker->optional()->file(
            base_path('resources/placeholders/hotels'),
            $path,
            false
        ) ?: $imageUrl;
    }
}
