<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;

class ReviewsTableSeeder extends Seeder
{
    public function run()
    {
        $count = (int) config('seeder.reviews_count', 50);

        if ($count <= 0) {
            $this->command->error("Invalid review count: {$count}. Must be greater than 0.");
            return;
        }

        $this->command->info("Seeding {$count} reviews...");

        try {
            $this->command->getOutput()->progressStart($count);

            \DB::transaction(function () use ($count) {
                Review::factory()->count($count)->create();
            });

            $this->command->getOutput()->progressFinish();

            $this->command->info("Successfully seeded {$count} reviews.");
        } catch (\Exception $e) {
            $this->command->error("Failed to seed reviews: " . $e->getMessage());
        }
    }
}
