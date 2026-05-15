<?php

namespace Database\Factories;

use App\Models\NotificationLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationLogFactory extends Factory
{
    protected $model = NotificationLog::class;

    public function definition()
    {
        return [
            'pet_id' => \App\Models\Pet::factory(),
            'tutor_id' => null,
            'type' => 'vaccination_reminder',
            'channel' => 'email',
            'destination' => $this->faker->email,
            'message' => $this->faker->text,
            'status' => 'pending',
            'sent_at' => now(),
        ];
    }
}
