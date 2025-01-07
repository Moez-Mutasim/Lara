<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Passport;
use App\Models\User;

class PassportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        if (User::count() === 0) {
            $this->command->warn('No users found. Creating some users first...');
            User::factory()->count(10)->create();
        }

        Passport::factory()->count(20)->create();
    }
}
