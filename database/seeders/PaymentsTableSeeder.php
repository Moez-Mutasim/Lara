<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\Booking;

class PaymentsTableSeeder extends Seeder
{
    public function run()
    {
        $count = config('seeder.payments_count', 50);

        if (Booking::count() === 0) {
            $this->command->warn("No bookings found. Creating bookings...");
            Booking::factory()->count(10)->create();
        }

        try {
            $this->command->info("Seeding {$count} payments...");
            Payment::factory()
                ->count($count)
                ->state(function () {
                    return [
                        'booking_id' => Booking::inRandomOrder()->first()->booking_id,
                    ];
                })
                ->create();
            $this->command->info("Successfully seeded {$count} payments.");
        } catch (\Exception $e) {
            $this->command->error("Failed to seed payments: " . $e->getMessage());
        }
    }
}
