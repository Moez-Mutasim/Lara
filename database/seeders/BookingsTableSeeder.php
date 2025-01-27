<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\User;
use App\Models\Flight;
use App\Models\Hotel;
use App\Models\Car;

class BookingsTableSeeder extends Seeder
{
    public function run()
    {
        $count = config('seeder.bookings_count', 50);

        $this->ensureDependentData();

        try {
            $this->command->info("Seeding {$count} bookings...");
            Booking::factory()->count($count)->create();
            $this->command->info("Successfully seeded {$count} bookings.");
        } catch (\Exception $e) {
            $this->command->error("Failed to seed bookings: " . $e->getMessage());
        }
    }

    private function ensureDependentData()
    {
        if (User::count() === 0) {
            $this->command->info("Creating users...");
            User::factory()->count(10)->create();
        }

        if (Flight::count() === 0) {
            $this->command->info("Creating flights...");
            Flight::factory()->count(10)->create();
        }

        if (Hotel::count() === 0) {
            $this->command->info("Creating hotels...");
            Hotel::factory()->count(10)->create();
        }

        if (Car::count() === 0) {
            $this->command->info("Creating cars...");
            Car::factory()->count(10)->create();
        }
    }
}
