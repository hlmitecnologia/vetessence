<?php

namespace Database\Factories;

use App\Models\DentalChart;
use App\Models\Pet;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class DentalChartFactory extends Factory
{
    protected $model = DentalChart::class;

    public function definition()
    {
        return [
            'pet_id' => Pet::factory(),
            'vet_id' => User::factory(),
            'examination_date' => $this->faker->date(),
            'procedure_type' => 'consultation',
            'general_notes' => $this->faker->sentence(),
            'branch_id' => Branch::factory(),
        ];
    }
}
