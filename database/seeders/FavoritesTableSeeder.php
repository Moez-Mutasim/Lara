<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Favorite;
use App\Models\User;


class FavoritesTableSeeder extends Seeder
{
    public function run()
    {
        if (User::count() === 0) {
            $this->command->warn('No users found. Creating some users first...');
            User::factory()->count(10)->create();
        }
        $favoriteCount = config('seeder.favorite_count', 50);
        $this->command->info("Seeding {$favoriteCount} favorites...");
        Favorite::factory()->count($favoriteCount)->create();
        $this->command->info("Successfully seeded {$favoriteCount} favorites.");
    }
}