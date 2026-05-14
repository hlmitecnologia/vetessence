<?php

namespace Database\Factories;

use App\Models\Teleconsultation;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeleconsultationFactory extends Factory
{
    protected $model = Teleconsultation::class;

    public function definition()
    {
        return [
            'room_name' => $this->faker->word,
            'room_token' => \App\Models\Teleconsultation::generateRoomToken(),
            'pet_id' => \App\Models\Pet::factory(),
            'vet_id' => \App\Models\User::factory(),
            'status' => 'scheduled',
            'provider' => 'jitsi',
            'scheduled_at' => now()->addDay(),
        ];
    }
}
