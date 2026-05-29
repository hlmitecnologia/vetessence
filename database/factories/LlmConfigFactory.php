<?php

namespace Database\Factories;

use App\Models\LlmConfig;
use Illuminate\Database\Eloquent\Factories\Factory;

class LlmConfigFactory extends Factory
{
    protected $model = LlmConfig::class;

    public function definition()
    {
        return [
            'provider' => 'openai',
            'is_active' => true,
            'temperature' => 0.3,
            'max_tokens' => 500,
            'openai_api_key' => $this->faker->uuid(),
            'openai_model' => 'gpt-4o-mini',
        ];
    }
}
