<?php

namespace Database\Factories;

use App\Models\Pet;
use App\Models\Vaccination;
use App\Models\VaccinationReminder;
use Illuminate\Database\Eloquent\Factories\Factory;

class VaccinationReminderFactory extends Factory
{
    protected $model = VaccinationReminder::class;

    public function definition()
    {
        return [
            'vaccination_id' => Vaccination::factory(),
            'pet_id' => Pet::factory(),
            'scheduled_date' => $this->faker->date(),
            'status' => 'pending',
        ];
    }
}
