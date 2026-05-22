<?php

namespace Database\Factories;

use App\Models\BreedDefault;
use App\Models\Pet;
use Illuminate\Database\Eloquent\Factories\Factory;

class PetFactory extends Factory
{
    protected $model = Pet::class;

    public function definition()
    {
        $species = $this->faker->randomElement(['canine', 'feline']);
        $breed = BreedDefault::where('species', $species)->where('is_active', true)->inRandomOrder()->first();

        return [
            'name' => $this->faker->firstName,
            'species' => $species,
            'breed' => $breed?->breed,
            'breed_default_id' => $breed?->id,
            'gender' => $this->faker->randomElement(['male', 'female']),
            'birth_date' => $this->faker->dateTimeBetween('-5 years', '-1 month'),
            'microchip_number' => $this->faker->optional(0.3)->numerify('####-####-####-####'),
            'rg_number' => $this->faker->optional(0.2)->bothify('RG-####?-?#'),
            'coat' => $this->faker->optional()->randomElement(['short', 'medium', 'long']),
            'size' => $this->faker->optional()->randomElement(['small', 'medium', 'large']),
        ];
    }
}
