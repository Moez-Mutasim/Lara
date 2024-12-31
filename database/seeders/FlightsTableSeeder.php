<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Flight;

class FlightsTableSeeder extends Seeder
{
    public function run()
    {
        $count = (int) config('seeder.flights_count', 50);

        $this->command->info("Seeding {$count} flights...");

        try {
            Flight::factory()->count($count)->create();
            $this->command->info("Successfully seeded {$count} flights.");
        } catch (\Exception $e) {
            $this->command->error("Failed to seed flights: " . $e->getMessage());
        }
    }
}
