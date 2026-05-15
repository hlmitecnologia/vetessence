<?php

namespace Database\Factories;

use App\Models\Exam;
use App\Models\Pet;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamFactory extends Factory
{
    protected $model = Exam::class;

    public function definition()
    {
        return [
            'pet_id' => Pet::factory(),
            'vet_id' => User::factory(),
            'type' => $this->faker->word(),
            'requested_date' => $this->faker->date(),
            'status' => 'requested',
            'notes' => $this->faker->sentence(),
            'branch_id' => Branch::factory(),
        ];
    }
}
