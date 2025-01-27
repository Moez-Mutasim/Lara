<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $count = (int) config('seeder.users_count', 50);

        if ($count <= 0) {
            $this->command->error("Invalid user count: {$count}. Must be greater than 0.");
            return;
        }

        $this->command->info("Seeding {$count} users...");

        try {
            $this->command->getOutput()->progressStart($count);

            \DB::transaction(function () use ($count) {
                User::create([
                    'name' => 'admin',
                    'email' => 'a@example.com',
                    'phone' => '1234567890',
                    'password' => Hash::make('admin123456'),
                    'gender' => 'male',
                    'role' => 'admin',
                    'date_of_birth' => '1990-05-15',
                ]);

                User::factory()->admin()->count(2)->create();
                User::factory()->customer()->count($count - 3)->create();
            });

            $this->command->getOutput()->progressFinish();

            $this->command->info("Successfully seeded {$count} users.");
        } catch (\Exception $e) {
            $this->command->error("Failed to seed users: " . $e->getMessage());
        }
    }
}
