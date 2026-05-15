<?php

namespace Database\Factories;

use App\Models\TriageRecord;
use App\Models\Pet;
use Illuminate\Database\Eloquent\Factories\Factory;

class TriageRecordFactory extends Factory
{
    protected $model = TriageRecord::class;

    public function definition()
    {
        return [
            'pet_id' => Pet::factory(),
            'check_in_at' => now(),
            'severity' => $this->faker->randomElement(['green', 'yellow', 'orange', 'red']),
            'chief_complaint' => $this->faker->sentence(),
            'status' => 'waiting',
        ];
    }
}
