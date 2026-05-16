<?php

namespace Database\Factories;

use App\Models\DrugFormulary;
use Illuminate\Database\Eloquent\Factories\Factory;

class DrugFormularyFactory extends Factory
{
    protected $model = DrugFormulary::class;

    public function definition()
    {
        return [
            'drug' => $this->faker->word,
            'species' => $this->faker->randomElement(['Canina', 'Felina', 'Equina']),
            'dosage_mg_kg' => $this->faker->randomFloat(2, 0.1, 10),
            'max_dose' => $this->faker->randomFloat(2, 10, 200),
            'route' => $this->faker->randomElement(['VO', 'SC', 'IM', 'IV']),
            'is_active' => true,
        ];
    }
}
