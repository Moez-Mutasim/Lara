<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Passport;
use App\Models\User;

class PassportsTableSeeder extends Seeder
{
    public function run()
    {
        if (User::count() === 0) {
            $this->command->warn('No users found. Creating some users first...');
            User::factory()->count(10)->create();
        }

        $passportCount = config('seeder.passports_count', 50);

        $this->command->info("Seeding {$passportCount} passports...");

        try {
            Passport::factory()->count($passportCount)->create();
            $this->command->info("Successfully seeded {$passportCount} passports.");
        } catch (\Exception $e) {
            $this->command->error("Failed to seed passports: " . $e->getMessage());
        }
    }
}
