<?php

namespace Database\Factories;

use App\Models\Prescription;
use App\Models\MedicalRecord;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class PrescriptionFactory extends Factory
{
    protected $model = Prescription::class;

    public function definition()
    {
        return [
            'medical_record_id' => MedicalRecord::factory(),
            'medication' => $this->faker->word(),
            'dosage' => $this->faker->randomNumber(2) . 'mg',
            'unit' => 'mg',
            'frequency' => '8/8h',
            'duration' => '7 dias',
            'route' => 'oral',
            'instructions' => $this->faker->sentence(),
            'created_by' => User::factory(),
            'branch_id' => Branch::factory(),
        ];
    }
}
