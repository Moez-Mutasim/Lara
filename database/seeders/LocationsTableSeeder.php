<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationsTableSeeder extends Seeder
{
    public function run()
    {
        $count = (int) config('seeder.locations_count', 20);

        if ($count <= 0) {
            $this->command->error("Invalid location count: {$count}. Must be greater than 0.");
            return;
        }

        $this->command->info("Seeding {$count} locations...");

        try {
            $this->command->getOutput()->progressStart($count);

            \DB::transaction(function () use ($count) {
                Location::factory()->count($count)->create();
            });

            $this->command->getOutput()->progressFinish();

            $this->command->info("Successfully seeded {$count} locations.");
        } catch (\Exception $e) {
            $this->command->error("Failed to seed locations: " . $e->getMessage());
        }
    }
}
