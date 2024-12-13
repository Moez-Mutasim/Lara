<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountriesTableSeeder extends Seeder
{
    public function run()
    {
        $count = (int) config('seeder.countries_count', 20);

        $predefinedCountries = [
            [
                'name' => 'United States',
                'code' => 'US',
                'iso_alpha_3' => 'USA',
                'continent' => 'North America',
                'currency' => 'USD',
                'is_active' => true,
            ],
            [
                'name' => 'Canada',
                'code' => 'CA',
                'iso_alpha_3' => 'CAN',
                'continent' => 'North America',
                'currency' => 'CAD',
                'is_active' => true,
            ],
            [
                'name' => 'United Kingdom',
                'code' => 'GB',
                'iso_alpha_3' => 'GBR',
                'continent' => 'Europe',
                'currency' => 'GBP',
                'is_active' => true,
            ],
            [
                'name' => 'Australia',
                'code' => 'AU',
                'iso_alpha_3' => 'AUS',
                'continent' => 'Oceania',
                'currency' => 'AUD',
                'is_active' => true,
            ],
            [
                'name' => 'India',
                'code' => 'IN',
                'iso_alpha_3' => 'IND',
                'continent' => 'Asia',
                'currency' => 'INR',
                'is_active' => true,
            ],
            [
                'name' => 'South Africa',
                'code' => 'ZA',
                'iso_alpha_3' => 'ZAF',
                'continent' => 'Africa',
                'currency' => 'ZAR',
                'is_active' => true,
            ],
            [
                'name' => 'Brazil',
                'code' => 'BR',
                'iso_alpha_3' => 'BRA',
                'continent' => 'South America',
                'currency' => 'BRL',
                'is_active' => true,
            ],
        ];

        $this->command->info("Seeding predefined countries...");
        foreach ($predefinedCountries as $country) {
            Country::firstOrCreate(['code' => $country['code']], $country);
        }
        $this->command->info("Predefined countries seeded.");

        try {
            $this->command->info("Seeding {$count} random countries...");
            Country::factory()->count($count)->create();
            $this->command->info("Successfully seeded {$count} random countries.");
        } catch (\Exception $e) {
            $this->command->error("Failed to seed random countries: " . $e->getMessage());
        }
    }
}
