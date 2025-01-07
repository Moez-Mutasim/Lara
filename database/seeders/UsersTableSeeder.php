<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $count = (int) config('seeder.users_count', 50);

        if ($count <= 0) {
            $this->command->error("Invalid user count: {$count}. Must be greater than 0.");
            return;}

        $this->command->info("Seeding {$count} users...");

        try {
            $this->command->getOutput()->progressStart($count);

            \DB::transaction(function () use ($count) {
                // Create agents
                User::factory()->agent()->count($count - 3)->create();

                // Create admins
                User::factory()->admin()->count(3)->create();
            });

            $this->command->getOutput()->progressFinish();

            $this->command->info("Successfully seeded {$count} users.");
        } catch (\Exception $e) {
            $this->command->error("Failed to seed users: " . $e->getMessage());
        }
    }
}
