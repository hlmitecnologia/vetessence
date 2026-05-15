<?php

namespace Database\Factories;

use App\Models\Surgery;
use App\Models\Pet;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class SurgeryFactory extends Factory
{
    protected $model = Surgery::class;

    public function definition()
    {
        return [
            'pet_id' => Pet::factory(),
            'vet_id' => User::factory(),
            'surgery_type' => $this->faker->word(),
            'status' => 'scheduled',
            'scheduled_date' => $this->faker->date(),
            'cost' => 0,
            'diagnosis' => $this->faker->sentence(),
            'surgery_notes' => $this->faker->sentence(),
            'branch_id' => Branch::factory(),
        ];
    }
}
