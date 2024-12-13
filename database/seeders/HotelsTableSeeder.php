<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hotel;

class HotelsTableSeeder extends Seeder
{
    public function run()
    {
        $count = (int) config('seeder.hotels_count', 20);

        $this->command->info("Seeding {$count} hotels...");

        try {
            Hotel::factory()->count($count)->create();
            $this->command->info("Successfully seeded {$count} hotels.");
        } catch (\Exception $e) {
            $this->command->error("Failed to seed hotels: " . $e->getMessage());
        }
    }
}
