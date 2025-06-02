<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class CityFactory extends Factory
{
    protected $model = City::class;

    public function definition(): array
    {
        return [
            'nom' => $this->faker->city(),
            'code_postal' => $this->faker->postcode(),
            'population' => $this->faker->numberBetween(1000, 100000),
            'department_id' => Department::factory(),
        ];
    }
}