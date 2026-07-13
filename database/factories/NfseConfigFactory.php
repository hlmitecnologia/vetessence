<?php

namespace Database\Factories;

use App\Models\NfseConfig;
use Illuminate\Database\Eloquent\Factories\Factory;

class NfseConfigFactory extends Factory
{
    protected $model = NfseConfig::class;

    public function definition()
    {
        return [
            'provider' => 'webmania',
            'ambiente' => 'homologacao',
            'webmania_access_token' => $this->faker->uuid(),
            'is_active' => true,
        ];
    }
}
