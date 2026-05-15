<?php

namespace Database\Factories;

use App\Models\AnesthesiaMonitoring;
use App\Models\Pet;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnesthesiaMonitoringFactory extends Factory
{
    protected $model = AnesthesiaMonitoring::class;

    public function definition()
    {
        return [
            'surgery_id' => \App\Models\Surgery::factory(),
            'pet_id' => Pet::factory(),
            'vet_id' => User::factory(),
            'monitoring_start' => $this->faker->dateTime(),
            'monitoring_end' => $this->faker->dateTime(),
            'premedication' => $this->faker->word(),
            'induction_agent' => $this->faker->word(),
            'maintenance_agent' => $this->faker->word(),
            'observations' => $this->faker->sentence(),
            'branch_id' => Branch::factory(),
        ];
    }
}
