<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Search;
use App\Models\User;

class SearchesTableSeeder extends Seeder
{
    public function run()
    {
        if (User::count() === 0) {
            $this->command->warn('No users found. Creating some users first...');
            User::factory()->count(10)->create();
        }
        $searchCount = config('seeder.search_count', 50);
        $this->command->info("Seeding {$searchCount} searches...");
        Search::factory()->count($searchCount)->create();
        $this->command->info("Successfully seeded {$searchCount} searches.");
    }
}
