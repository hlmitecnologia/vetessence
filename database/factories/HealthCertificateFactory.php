<?php

namespace Database\Factories;

use App\Models\HealthCertificate;
use Illuminate\Database\Eloquent\Factories\Factory;

class HealthCertificateFactory extends Factory
{
    protected $model = HealthCertificate::class;

    public function definition()
    {
        return [
            'certificate_number' => HealthCertificate::generateNumber(),
            'pet_id' => \App\Models\Pet::factory(),
            'type' => 'international',
            'destination' => $this->faker->country,
            'issuer_vet_id' => \App\Models\User::factory(),
            'issue_date' => now(),
            'expiration_date' => now()->addDays(30),
            'clinical_notes' => $this->faker->sentence,
            'is_export' => false,
            'status' => 'draft',
        ];
    }
}
