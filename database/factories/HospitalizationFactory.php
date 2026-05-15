<?php

namespace Database\Factories;

use App\Models\Hospitalization;
use App\Models\Pet;
use App\Models\Tutor;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class HospitalizationFactory extends Factory
{
    protected $model = Hospitalization::class;

    public function definition()
    {
        return [
            'pet_id' => Pet::factory(),
            'tutor_id' => Tutor::factory(),
            'vet_id' => User::factory(),
            'appointment_id' => null,
            'admission_date' => $this->faker->date(),
            'admission_reason' => $this->faker->sentence(),
            'initial_diagnosis' => $this->faker->sentence(),
            'department' => $this->faker->word(),
            'bed' => $this->faker->word(),
            'is_emergency' => false,
            'status' => 'active',
            'branch_id' => Branch::factory(),
        ];
    }
}
