<?php

namespace Database\Factories;

use App\Models\Tutor;
use Illuminate\Database\Eloquent\Factories\Factory;

class TutorFactory extends Factory
{
    protected $model = Tutor::class;

    public function definition()
    {
        return [
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->email,
            'city' => $this->faker->city,
            'state' => 'SP',
        ];
    }
}
