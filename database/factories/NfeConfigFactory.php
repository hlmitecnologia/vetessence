<?php

namespace Database\Factories;

use App\Models\NfeConfig;
use Illuminate\Database\Eloquent\Factories\Factory;

class NfeConfigFactory extends Factory
{
    protected $model = NfeConfig::class;

    public function definition()
    {
        return [
            'provider' => 'webmania',
            'ambiente' => 'homologacao',
            'webmania_consumer_key' => $this->faker->uuid(),
            'webmania_consumer_secret' => $this->faker->uuid(),
            'webmania_access_token' => $this->faker->uuid(),
            'webmania_access_token_secret' => $this->faker->uuid(),
            'is_active' => true,
        ];
    }
}
