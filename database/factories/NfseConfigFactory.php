<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\NfseConfig;
use Illuminate\Database\Eloquent\Factories\Factory;

class NfseConfigFactory extends Factory
{
    protected $model = NfseConfig::class;

    public function definition()
    {
        return [
            'branch_id' => Branch::factory(),
            'cnpj' => $this->faker->numerify('##.###.###/####-##'),
            'municipio_ibge' => '3550308',
            'regime_tributario' => 'simples_nacional',
            'serie' => '1',
            'ambiente' => 'homologacao',
            'webmania_app_id' => $this->faker->uuid(),
            'webmania_app_secret' => $this->faker->uuid(),
            'webmania_consumer_key' => $this->faker->uuid(),
            'webmania_consumer_secret' => $this->faker->uuid(),
            'is_active' => true,
        ];
    }
}
