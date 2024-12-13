<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Car;

class CarsTableSeeder extends Seeder
{
    public function run()
    {
        $count = (int) config('seeder.cars_count', 20);

        $this->command->info("Seeding {$count} cars...");

        try {
            Car::factory()->count($count)->create();
            $this->command->info("Successfully seeded {$count} cars.");
        } catch (\Exception $e) {
            $this->command->error("Failed to seed cars: " . $e->getMessage());
        }
    }
}
