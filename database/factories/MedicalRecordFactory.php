<?php

namespace Database\Factories;

use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class MedicalRecordFactory extends Factory
{
    protected $model = MedicalRecord::class;

    public function definition()
    {
        return [
            'pet_id' => Pet::factory(),
            'user_id' => User::factory(),
            'appointment_id' => null,
            'date' => $this->faker->date(),
            'type' => 'consultation',
            'complaint' => $this->faker->sentence(),
            'diagnosis' => $this->faker->sentence(),
            'treatment' => $this->faker->sentence(),
            'notes' => $this->faker->sentence(),
            'branch_id' => Branch::factory(),
        ];
    }

    public function withVet()
    {
        return $this->state(fn(array $attributes) => [
            'user_id' => User::factory()->create(),
        ]);
    }
}
