<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    protected $model = Branch::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company() . ' Branch',
            'location' => $this->faker->address(),
            'manager' => $this->faker->name(),
            'phone' => $this->faker->e164PhoneNumber(),
            'is_active' => $this->faker->boolean(90),
        ];
    }
}
