<?php

namespace Database\Factories;

use App\Models\Boarding;
use Illuminate\Database\Eloquent\Factories\Factory;

class BoardingFactory extends Factory
{
    protected $model = Boarding::class;

    public function definition()
    {
        return [
            'pet_id' => \App\Models\Pet::factory(),
            'type' => 'boarding',
            'check_in_at' => now(),
            'status' => 'checked_in',
            'daily_rate' => 50,
            'grooming_fee' => 0,
            'total_amount' => 50,
            'created_by' => \App\Models\User::factory(),
        ];
    }
}
