<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition()
    {
        return [
            'pet_id' => Pet::factory(),
            'vet_id' => User::factory(),
            'date' => $this->faker->date(),
            'time' => $this->faker->time('H:i'),
            'type' => $this->faker->randomElement(['consulta', 'retorno', 'emergencia', 'cirurgia', 'vacina', 'exame']),
            'status' => $this->faker->randomElement(['scheduled', 'confirmed', 'completed']),
            'reason' => $this->faker->sentence(),
        ];
    }
}
