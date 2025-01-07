<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;

class NotificationsTableSeeder extends Seeder
{
    public function run()
    {
        $count = config('seeder.notifications_count', 50);

        if ($count <= 0) {
            $this->command->error("Invalid notification count: {$count}. Must be greater than 0.");
            return;
        }

        $this->command->info("Seeding {$count} notifications...");

        try {
            \DB::transaction(function () use ($count) {
                Notification::factory()->count($count)->create();
            });

            $this->command->info("Successfully seeded {$count} notifications.");
        } catch (\Exception $e) {
            $this->command->error("Failed to seed notifications: " . $e->getMessage());
        }
    }
}
