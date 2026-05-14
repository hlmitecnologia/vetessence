<?php

namespace Database\Factories;

use App\Models\StaffTimeOff;
use Illuminate\Database\Eloquent\Factories\Factory;

class StaffTimeOffFactory extends Factory
{
    protected $model = StaffTimeOff::class;

    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'start_date' => $this->faker->dateTimeBetween('+1 week', '+1 week'),
            'end_date' => $this->faker->dateTimeBetween('+4 days', '+4 days'),
            'type' => 'ferias',
            'status' => 'pending',
        ];
    }
}
