<?php

namespace Database\Factories;

use App\Models\ImagingExam;
use App\Models\Pet;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class ImagingExamFactory extends Factory
{
    protected $model = ImagingExam::class;

    public function definition()
    {
        return [
            'exam_number' => 'IMG-' . date('Ymd') . '-' . str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT),
            'pet_id' => Pet::factory(),
            'vet_id' => User::factory(),
            'exam_type' => $this->faker->word(),
            'exam_date' => $this->faker->date(),
            'region' => $this->faker->word(),
            'findings' => $this->faker->sentence(),
            'status' => 'requested',
            'branch_id' => Branch::factory(),
        ];
    }
}
