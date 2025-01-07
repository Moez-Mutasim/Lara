<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Car;

class CarsTableSeeder extends Seeder
{
    public function run()
    {
        $count = config('seeder.cars_count', 50);

        if ($count <= 0) {
            $this->command->error("Invalid car count: {$count}. Must be greater than 0.");
            return;
        }

        $this->command->info("Seeding {$count} cars...");

        try {
            \DB::transaction(function () use ($count) {
                Car::factory()->count($count)->create();
            });

            $this->command->info("Successfully seeded {$count} cars.");
        } catch (\Exception $e) {
            $this->command->error("Failed to seed cars: " . $e->getMessage());
        }
    }
}
