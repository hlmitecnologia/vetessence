<?php

namespace Database\Factories;

use App\Models\ParasiteControl;
use Illuminate\Database\Eloquent\Factories\Factory;

class ParasiteControlFactory extends Factory
{
    protected $model = ParasiteControl::class;

    public function definition()
    {
        return [
            'pet_id' => \App\Models\Pet::factory(),
            'product_name' => $this->faker->word,
            'type' => $this->faker->randomElement(['flea', 'tick', 'heartworm', 'intestinal', 'combination']),
            'application_date' => now(),
            'next_due_date' => now()->addMonths(3),
            'vet_id' => \App\Models\User::factory(),
            'notes' => $this->faker->sentence,
        ];
    }
}
