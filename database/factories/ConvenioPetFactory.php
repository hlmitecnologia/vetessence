<?php

namespace Database\Factories;

use App\Models\ConvenioPet;
use App\Models\Convenio;
use App\Models\Pet;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConvenioPetFactory extends Factory
{
    protected $model = ConvenioPet::class;

    public function definition()
    {
        return [
            'convenio_id' => Convenio::factory(),
            'pet_id' => Pet::factory(),
        ];
    }
}
