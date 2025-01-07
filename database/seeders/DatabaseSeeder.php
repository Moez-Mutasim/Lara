<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    
    public function run()
    {
        $seeders = [
            CountriesTableSeeder::class,
            LocationsTableSeeder::class,
            UsersTableSeeder::class,
            FlightsTableSeeder::class,
            HotelsTableSeeder::class,
            CarsTableSeeder::class,
            BookingsTableSeeder::class,
            NotificationsTableSeeder::class,
            PaymentsTableSeeder::class,
            ReviewsTableSeeder::class,
            PassportsTableSeeder::class,
            SearchSeeder::class,
            FavoriteSeeder::class,
        ];

        if ($seedersToRun = $this->command->option('seeders')) {
            $seeders = array_filter(
                $seeders,
                fn ($seeder) => in_array(class_basename($seeder), explode(',', $seedersToRun))
            );
        }

        foreach ($seeders as $seeder) {
            $this->command->info("Running {$seeder}...");
            $start = microtime(true);

            try {
                $this->call($seeder);
                $duration = round(microtime(true) - $start, 2);
                $this->command->info("Finished {$seeder} in {$duration} seconds.");
            } catch (\Exception $e) {
                $this->command->error("Failed to run {$seeder}: {$e->getMessage()}");
            }
        }

        $this->command->info("Database seeding completed.");
    }
}
