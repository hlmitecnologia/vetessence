<?php

namespace Database\Factories;

use App\Models\WeightRecord;
use App\Models\Pet;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class WeightRecordFactory extends Factory
{
    protected $model = WeightRecord::class;

    public function definition()
    {
        return [
            'pet_id' => Pet::factory(),
            'weight' => $this->faker->randomFloat(2, 1, 100),
            'bcs' => $this->faker->numberBetween(1, 9),
            'measurement_date' => $this->faker->date(),
            'notes' => $this->faker->sentence(),
            'branch_id' => Branch::factory(),
        ];
    }
}
