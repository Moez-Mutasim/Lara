<?php

namespace Database\Factories;

use App\Models\Passport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PassportFactory extends Factory
{
    protected $model = Passport::class;

    public function definition()
    {
        $user = User::inRandomOrder()->first() ?? User::factory()->create();

        return [
            'user_id' => User::inRandomOrder()->first()->user_id ?? User::factory(),
            'passport_number' => $this->faker->unique()->regexify('[A-Z]{2}[0-9]{6}'),
            'full_name' => $this->faker->name,
            'country_of_issue' => $this->faker->country(),
            'issue_date' => $this->faker->dateTimeBetween('-10 years', 'now')->format('Y-m-d'),
            'expiry_date' => $this->faker->dateTimeBetween('now', '+10 years')->format('Y-m-d'),
            'passport_image' => $this->faker->imageUrl(200, 300, 'passport'),
        ];
    }
}
