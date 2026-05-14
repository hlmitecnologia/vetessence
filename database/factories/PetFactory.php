<?php

namespace Database\Factories;

use App\Models\Pet;
use Illuminate\Database\Eloquent\Factories\Factory;

class PetFactory extends Factory
{
    protected $model = Pet::class;

    public function definition()
    {
        return [
            'name' => $this->faker->firstName,
            'species' => $this->faker->randomElement(['canine', 'feline']),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'birth_date' => $this->faker->dateTimeBetween('-5 years', '-1 month'),
        ];
    }
}
