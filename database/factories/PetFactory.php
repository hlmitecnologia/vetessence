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
            'microchip_number' => $this->faker->optional(0.3)->numerify('####-####-####-####'),
            'rg_number' => $this->faker->optional(0.2)->bothify('RG-####?-?#'),
            'coat' => $this->faker->optional()->randomElement(['short', 'medium', 'long']),
            'size' => $this->faker->optional()->randomElement(['small', 'medium', 'large']),
        ];
    }
}
