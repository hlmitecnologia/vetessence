<?php

namespace Database\Factories;

use App\Models\ExecutionMap;
use App\Models\Hospitalization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExecutionMapFactory extends Factory
{
    protected $model = ExecutionMap::class;

    public function definition()
    {
        return [
            'hospitalization_id' => Hospitalization::factory(),
            'date' => $this->faker->date(),
            'notes' => $this->faker->sentence(),
            'created_by' => User::factory(),
        ];
    }
}
