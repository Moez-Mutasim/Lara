<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->unique()->e164PhoneNumber(),
            'password' => Hash::make('password'),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'date_of_birth' => $this->faker->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
            'profile_picture' => $this->faker->optional()->imageUrl(150, 150),
            'role' => $this->faker->randomElement(['guest', 'user', 'admin']),
        ];
    }

    
    public function admin()
    {return $this->state(fn () => ['role' => 'admin']);}

    public function guest()
    {return $this->state(fn () => ['role' => 'guest']);}

    public function user()
    {return $this->state(fn () => ['role' => 'user']);}
}
