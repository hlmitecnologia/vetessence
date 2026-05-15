<?php

namespace Database\Factories;

use App\Models\ConsentForm;
use App\Models\Pet;
use App\Models\Tutor;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConsentFormFactory extends Factory
{
    protected $model = ConsentForm::class;

    public function definition()
    {
        return [
            'consent_number' => 'CON-' . date('Ymd') . '-' . str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT),
            'pet_id' => Pet::factory(),
            'client_name' => $this->faker->name(),
            'tutor_id' => Tutor::factory(),
            'consent_template_id' => null,
            'veterinarian_id' => User::factory(),
            'status' => 'pending',
            'branch_id' => Branch::factory(),
        ];
    }
}
