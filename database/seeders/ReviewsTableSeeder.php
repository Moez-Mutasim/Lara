<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\Flight;
use App\Models\Hotel;
use App\Models\Car;
use App\Models\User;

class ReviewsTableSeeder extends Seeder
{
    public function run()
    {
        $count = config('seeder.reviews_count', 50);

        if (User::count() === 0) {
            $this->command->warn("No users found. Creating some users first...");
            User::factory()->count(10)->create();
        }

        if ($count <= 0) {
            $this->command->error("Invalid review count: {$count}. Must be greater than 0.");
            return;
        }

        $this->command->info("Seeding {$count} reviews...");

        try {
            \DB::transaction(function () use ($count) {
                Review::factory()
                    ->count($count)
                    ->state(function () {
                        $entityTypes = ['flight', 'hotel', 'car'];
                        $selectedType = $entityTypes[array_rand($entityTypes)];

                        switch ($selectedType) {
                            case 'flight':
                                $flight = Flight::inRandomOrder()->first() ?? Flight::factory()->create();
                                return ['flight_id' => $flight->flight_id, 'hotel_id' => null, 'car_id' => null];

                            case 'hotel':
                                $hotel = Hotel::inRandomOrder()->first() ?? Hotel::factory()->create();
                                return ['hotel_id' => $hotel->hotel_id, 'flight_id' => null, 'car_id' => null];

                            case 'car':
                                $car = Car::inRandomOrder()->first() ?? Car::factory()->create();
                                return ['car_id' => $car->car_id, 'flight_id' => null, 'hotel_id' => null];
                        }
                    })
                    ->create();
            });

            $this->command->info("Successfully seeded {$count} reviews.");
        } catch (\Exception $e) {
            $this->command->error("Failed to seed reviews: " . $e->getMessage());
        }
    }
}
