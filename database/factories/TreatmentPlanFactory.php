<?php

namespace Database\Factories;

use App\Models\TreatmentPlan;
use App\Models\Pet;
use App\Models\Tutor;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class TreatmentPlanFactory extends Factory
{
    protected $model = TreatmentPlan::class;

    public function definition()
    {
        return [
            'plan_number' => 'PLN-' . date('Y') . '-' . str_pad(random_int(1, 99999), 5, '0', STR_PAD_LEFT),
            'pet_id' => Pet::factory(),
            'tutor_id' => Tutor::factory(),
            'vet_id' => User::factory(),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'total_estimated' => $this->faker->randomFloat(2, 100, 5000),
            'total_authorized' => $this->faker->randomFloat(2, 100, 5000),
            'status' => 'pending',
            'vet_notes' => $this->faker->sentence(),
            'branch_id' => Branch::factory(),
        ];
    }
}
