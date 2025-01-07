<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Flight;

class FlightsTableSeeder extends Seeder
{
    public function run()
    {
        $count = config('seeder.flights_count', 50);

        if ($count <= 0) {
            $this->command->error("Invalid flights count: {$count}. Must be greater than 0.");
            return;
        }

        $this->command->info("Seeding {$count} flights...");

        try {
            \DB::transaction(function () use ($count) {
                Flight::factory()->count($count)->create();
            });

            $this->command->info("Successfully seeded {$count} flights.");
        } catch (\Exception $e) {
            $this->command->error("Failed to seed flights: " . $e->getMessage());
        }
    }
}
