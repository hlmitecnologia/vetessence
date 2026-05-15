<?php

namespace Database\Factories;

use App\Models\ConvenioClaim;
use App\Models\ConvenioPet;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConvenioClaimFactory extends Factory
{
    protected $model = ConvenioClaim::class;

    public function definition()
    {
        return [
            'convenio_pet_id' => ConvenioPet::factory(),
            'claim_number' => 'CLM-' . $this->faker->unique()->numberBetween(100000, 999999),
            'status' => 'draft',
            'amount_requested' => $this->faker->randomFloat(2, 100, 5000),
        ];
    }
}
