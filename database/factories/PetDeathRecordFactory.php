<?php

namespace Database\Factories;

use App\Models\PetDeathRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

class PetDeathRecordFactory extends Factory
{
    protected $model = PetDeathRecord::class;

    public function definition()
    {
        return [
            'pet_id' => \App\Models\Pet::factory(),
            'death_date' => $this->faker->date(),
            'cause' => $this->faker->randomElement(['natural causes', 'euthanasia', 'accident', 'illness', 'old age']),
            'disposition' => 'cremation',
            'registered_by' => null,
        ];
    }
}
