<?php

namespace Database\Factories;

use App\Models\Vaccination;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class VaccinationFactory extends Factory
{
    protected $model = Vaccination::class;

    public function definition()
    {
        return [
            'pet_id' => Pet::factory(),
            'vet_id' => User::factory(),
            'vaccine' => $this->faker->word(),
            'batch' => $this->faker->bothify('??####'),
            'date' => $this->faker->date(),
            'next_date' => $this->faker->date(),
            'notes' => $this->faker->sentence(),
        ];
    }
}
