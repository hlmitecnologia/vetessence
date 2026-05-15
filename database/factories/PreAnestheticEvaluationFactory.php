<?php

namespace Database\Factories;

use App\Models\PreAnestheticEvaluation;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PreAnestheticEvaluationFactory extends Factory
{
    protected $model = PreAnestheticEvaluation::class;

    public function definition()
    {
        return [
            'pet_id' => Pet::factory(),
            'vet_id' => User::factory(),
            'asa_score' => $this->faker->randomElement([1, 2, 3]),
            'fasted' => $this->faker->boolean(),
            'hydrated' => $this->faker->boolean(),
            'status' => 'pending',
        ];
    }
}
