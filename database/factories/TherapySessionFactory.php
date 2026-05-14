<?php

namespace Database\Factories;

use App\Models\TherapySession;
use Illuminate\Database\Eloquent\Factories\Factory;

class TherapySessionFactory extends Factory
{
    protected $model = TherapySession::class;

    public function definition()
    {
        return [
            'pet_id' => \App\Models\Pet::factory(),
            'type' => $this->faker->randomElement(['physiotherapy', 'hydrotherapy', 'acupuncture', 'laser', 'massage']),
            'session_date' => $this->faker->dateTimeBetween('-1 week', '+1 week'),
            'therapist_id' => null,
            'duration_minutes' => $this->faker->numberBetween(30, 60),
            'status' => 'scheduled',
        ];
    }
}
