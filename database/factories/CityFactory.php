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
            'name' => $this->faker->city(),
            'postal_code' => str_pad($this->faker->numberBetween(1000, 99999), 5, '0', STR_PAD_LEFT),
            'population' => $this->faker->numberBetween(1000, 100000),
            'department_id' => Department::factory(),
        ];
    }
}