<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;

class BranchesTableSeeder extends Seeder
{
    public function run()
    {
        $count = (int) config('seeder.branches_count', 10);

        $this->command->info("Seeding {$count} branches...");

        try {
            $this->command->getOutput()->progressStart($count);

            \DB::transaction(function () use ($count) {
                Branch::factory()->count($count)->create();
            });

            $this->command->getOutput()->progressFinish();

            $this->command->info("Successfully seeded {$count} branches.");
        } catch (\Exception $e) {
            $this->command->error("Failed to seed branches: " . $e->getMessage());
        }
    }
}
