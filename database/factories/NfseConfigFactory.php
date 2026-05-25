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
            'webmania_app_id' => $this->faker->uuid(),
            'webmania_app_secret' => $this->faker->uuid(),
            'webmania_consumer_key' => $this->faker->uuid(),
            'webmania_consumer_secret' => $this->faker->uuid(),
            'is_active' => true,
        ];
    }
}
