<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;

class ReviewsTableSeeder extends Seeder
{
    public function run()
    {
        $count = config('seeder.reviews_count', 50);

        if ($count <= 0) {
            $this->command->error("Invalid review count: {$count}. Must be greater than 0.");
            return;
        }

        $this->command->info("Seeding {$count} reviews...");

        try {
            \DB::transaction(function () use ($count) {
                Review::factory()
                    ->count($count)
                    ->state(fn () => $this->randomReviewState())
                    ->create();
            });

            $this->command->info("Successfully seeded {$count} reviews.");
        } catch (\Exception $e) {
            $this->command->error("Failed to seed reviews: " . $e->getMessage());
        }
    }

    private function randomReviewState()
    {
        $entityTypes = ['flight', 'hotel', 'car'];
        $selectedType = $entityTypes[array_rand($entityTypes)];

        switch ($selectedType) {
            case 'flight':
                return ['flight_id' => Flight::factory()];
            case 'hotel':
                return ['hotel_id' => Hotel::factory()];
            case 'car':
                return ['car_id' => Car::factory()];
        }

        return [];
    }
}
