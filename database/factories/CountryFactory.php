<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

class CountryFactory extends Factory
{
   
    protected $model = Country::class;

    
    public function definition()
    {
        return [
            'name' => $this->faker->country(),
            'code' => $this->faker->unique()->countryCode(),
            'iso_alpha_3' => $this->faker->unique()->lexify('???'),
            'continent' => $this->faker->randomElement(['Africa', 'Asia', 'Europe', 'North America', 'Oceania', 'South America', 'Antarctica']),
            'currency' => $this->faker->currencyCode(),
            'is_active' => $this->faker->boolean(90),
        ];
    }
}
