<?php

namespace Database\Factories;

use App\Models\ExecutionLog;
use App\Models\ExecutionTask;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExecutionLogFactory extends Factory
{
    protected $model = ExecutionLog::class;

    public function definition()
    {
        return [
            'execution_task_id' => ExecutionTask::factory(),
            'performed_at' => $this->faker->dateTimeThisMonth(),
            'performed_by' => User::factory(),
            'status' => $this->faker->randomElement(['completed', 'skipped', 'partially']),
            'notes' => $this->faker->sentence(),
        ];
    }
}
