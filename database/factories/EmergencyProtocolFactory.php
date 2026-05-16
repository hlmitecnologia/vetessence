<?php

namespace Database\Factories;

use App\Models\EmergencyProtocol;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmergencyProtocolFactory extends Factory
{
    protected $model = EmergencyProtocol::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(3),
            'slug' => $this->faker->slug,
            'species' => $this->faker->randomElement(['Canina', 'Felina', 'Equina']),
            'severity' => $this->faker->randomElement(['critical', 'urgent', 'stable']),
            'procedure_steps' => $this->faker->paragraph,
            'is_active' => true,
        ];
    }
}
