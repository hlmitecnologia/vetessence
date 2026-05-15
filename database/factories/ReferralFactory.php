<?php

namespace Database\Factories;

use App\Models\Referral;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReferralFactory extends Factory
{
    protected $model = Referral::class;

    public function definition()
    {
        return [
            'pet_id' => Pet::factory(),
            'referring_vet_id' => User::factory(),
            'receiving_vet_id' => User::factory(),
            'referral_number' => 'REF-' . $this->faker->unique()->numerify('##########'),
            'reason' => $this->faker->sentence(),
            'status' => 'pending',
        ];
    }
}
