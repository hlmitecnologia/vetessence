<?php

namespace Database\Factories;

use App\Models\BoardingKennel;
use Illuminate\Database\Eloquent\Factories\Factory;

class BoardingKennelFactory extends Factory
{
    protected $model = BoardingKennel::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'size' => 'medium',
            'capacity' => 2,
            'is_active' => true,
        ];
    }
}
