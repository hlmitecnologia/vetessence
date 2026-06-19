<?php

namespace Database\Factories;

use App\Models\ExecutionTask;
use App\Models\ExecutionMap;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExecutionTaskFactory extends Factory
{
    protected $model = ExecutionTask::class;

    public function definition()
    {
        return [
            'execution_map_id' => ExecutionMap::factory(),
            'category' => $this->faker->randomElement(['medication', 'procedure', 'exam', 'care', 'other']),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->sentence(),
            'scheduled_time' => $this->faker->time('H:i'),
            'frequency' => $this->faker->randomElement(['every_8h', 'every_12h', 'every_6h', 'every_24h', 'once']),
            'route' => $this->faker->randomElement(['IV', 'IM', 'SC', 'VO', 'TOP']),
            'dosage' => (string) $this->faker->randomFloat(1, 0.1, 10),
            'unit' => $this->faker->randomElement(['ml', 'mg', 'UI', 'g']),
            'source_type' => 'manual',
            'source_id' => null,
            'status' => 'pending',
            'observations' => null,
            'sort_order' => 0,
            'created_by' => User::factory(),
        ];
    }

    public function completed()
    {
        return $this->state(fn () => ['status' => 'completed']);
    }

    public function overdue()
    {
        return $this->state(fn () => [
            'status' => 'pending',
            'scheduled_time' => now()->subHours(2)->format('H:i'),
        ]);
    }
}
