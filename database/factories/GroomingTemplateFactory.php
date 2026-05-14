<?php

namespace Database\Factories;

use App\Models\GroomingTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroomingTemplateFactory extends Factory
{
    protected $model = GroomingTemplate::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'species' => $this->faker->randomElement(['canino', 'felino']),
            'size' => $this->faker->randomElement(['pequeno', 'medio', 'grande']),
            'price' => $this->faker->randomFloat(2, 30, 150),
            'estimated_minutes' => $this->faker->numberBetween(30, 90),
            'is_active' => true,
        ];
    }
}
