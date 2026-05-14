<?php

namespace Database\Factories;

use App\Models\BreedDefault;
use Illuminate\Database\Eloquent\Factories\Factory;

class BreedDefaultFactory extends Factory
{
    protected $model = BreedDefault::class;

    public function definition()
    {
        return [
            'species' => $this->faker->randomElement(['canino', 'felino']),
            'breed' => $this->faker->word,
            'size' => $this->faker->randomElement(['pequeno', 'medio', 'grande']),
            'avg_weight_min' => $this->faker->numberBetween(5, 15),
            'avg_weight_max' => $this->faker->numberBetween(15, 40),
            'avg_lifespan_min' => $this->faker->numberBetween(8, 12),
            'avg_lifespan_max' => $this->faker->numberBetween(12, 18),
            'temperament' => $this->faker->sentence,
            'is_active' => true,
        ];
    }
}
