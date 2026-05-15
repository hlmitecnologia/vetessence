<?php

namespace Database\Factories;

use App\Models\PetDeathRecord;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PetDeathRecordFactory extends Factory
{
    protected $model = PetDeathRecord::class;

    public function definition()
    {
        return [
            'pet_id' => Pet::factory(),
            'death_date' => $this->faker->date(),
            'cause' => $this->faker->randomElement(['natural causes', 'euthanasia', 'accident', 'illness', 'old age']),
            'disposition' => 'cremation',
            'registered_by' => User::factory(),
        ];
    }

    public function withCremation()
    {
        return $this->state(fn(array $attrs) => [
            'cremation_type' => $this->faker->randomElement(['individual', 'coletiva']),
            'cremation_pickup_date' => $this->faker->dateTimeBetween('+1 day', '+7 days'),
            'memorial_text' => $this->faker->paragraph(),
        ]);
    }
}
