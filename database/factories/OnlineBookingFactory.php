<?php

namespace Database\Factories;

use App\Models\OnlineBooking;
use Illuminate\Database\Eloquent\Factories\Factory;

class OnlineBookingFactory extends Factory
{
    protected $model = OnlineBooking::class;

    public function definition()
    {
        return [
            'tutor_name' => $this->faker->name,
            'tutor_email' => $this->faker->email,
            'tutor_phone' => $this->faker->phoneNumber,
            'pet_name' => $this->faker->firstName,
            'pet_species' => 'canine',
            'preferred_date' => now()->addDays(3),
            'preferred_time' => '14:00',
            'status' => 'pending',
        ];
    }
}
