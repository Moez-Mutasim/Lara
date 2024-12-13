<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;

class PaymentsTableSeeder extends Seeder
{
    public function run()
    {
        $count = (int) config('seeder.payments_count', 20);

        if ($count <= 0) {
            $this->command->error("Invalid payment count: {$count}. Must be greater than 0.");
            return;
        }

        $this->command->info("Seeding {$count} payments...");

        try {
            $this->command->getOutput()->progressStart($count);

            \DB::transaction(function () use ($count) {
                Payment::factory()->count($count)->create();
            });

            $this->command->getOutput()->progressFinish();

            $this->command->info("Successfully seeded {$count} payments.");
        } catch (\Exception $e) {
            $this->command->error("Failed to seed payments: " . $e->getMessage());
        }
    }
}
