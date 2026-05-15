<?php

namespace Database\Factories;

use App\Models\Convenio;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConvenioFactory extends Factory
{
    protected $model = Convenio::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'is_active' => true,
        ];
    }
}
