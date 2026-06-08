<?php

namespace Database\Factories;

use App\Models\StaffSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

class StaffScheduleFactory extends Factory
{
    protected $model = StaffSchedule::class;

    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'work_date' => $this->faker->dateTimeBetween('-1 week', '+1 week'),
            'start_time' => '08:00',
            'end_time' => '18:00',
            'shift_type' => 'regular',
        ];
    }
}
