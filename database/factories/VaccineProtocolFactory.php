<?php

namespace Database\Factories;

use App\Models\VaccineProtocol;
use Illuminate\Database\Eloquent\Factories\Factory;

class VaccineProtocolFactory extends Factory
{
    protected $model = VaccineProtocol::class;

    public function definition()
    {
        return [
            'species' => $this->faker->randomElement(['canine', 'feline']),
            'vaccine_name' => $this->faker->word,
            'age_start_weeks' => $this->faker->numberBetween(4, 8),
            'age_end_weeks' => $this->faker->numberBetween(12, 52),
            'is_initial' => true,
            'dose_number' => 1,
            'booster_interval_months' => 12,
            'is_core' => true,
            'is_active' => true,
        ];
    }
}
