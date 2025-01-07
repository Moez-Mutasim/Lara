<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hotel;

class HotelsTableSeeder extends Seeder
{
    public function run()
    {
        $count = config('seeder.hotels_count', 50);

        if ($count <= 0) {
            $this->command->error("Invalid hotel count: {$count}. Must be greater than 0.");
            return;
        }

        $this->command->info("Seeding {$count} hotels...");

        try {
            \DB::transaction(function () use ($count) {
                Hotel::factory()->count($count)->create();
            });

            $this->command->info("Successfully seeded {$count} hotels.");
        } catch (\Exception $e) {
            $this->command->error("Failed to seed hotels: " . $e->getMessage());
        }
    }
}
