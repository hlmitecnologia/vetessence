<?php

namespace Database\Factories;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'model_type' => $this->faker->randomElement(['Pet', 'User', 'Tutor', 'Appointment', 'Invoice', 'MedicalRecord']),
            'model_id' => $this->faker->randomDigitNotNull,
            'action' => 'created',
            'old_values' => [],
            'new_values' => [],
            'ip_address' => '127.0.0.1',
            'created_at' => now(),
        ];
    }
}
