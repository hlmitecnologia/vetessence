<?php

namespace Database\Factories;

use App\Models\HospitalizationDailyRecord;
use App\Models\Hospitalization;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class HospitalizationDailyRecordFactory extends Factory
{
    protected $model = HospitalizationDailyRecord::class;

    public function definition()
    {
        return [
            'hospitalization_id' => Hospitalization::factory(),
            'user_id' => User::factory(),
            'record_date' => $this->faker->date(),
            'shift' => $this->faker->randomElement(['manha', 'tarde', 'noite']),
            'observations' => $this->faker->sentence(),
        ];
    }
}
