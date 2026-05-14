<?php

namespace Database\Factories;

use App\Models\DrugInteraction;
use Illuminate\Database\Eloquent\Factories\Factory;

class DrugInteractionFactory extends Factory
{
    protected $model = DrugInteraction::class;

    public function definition()
    {
        return [
            'drug_a' => $this->faker->word,
            'drug_b' => $this->faker->word,
            'severity' => $this->faker->randomElement(['contraindicated', 'caution', 'minor']),
            'description' => $this->faker->sentence,
            'mechanism' => $this->faker->sentence,
            'is_active' => true,
        ];
    }
}
