<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StateFactory extends Factory
{
    protected $model = \App\Models\State::class;

    public function definition(): array
    {
        return [
            'name' => fake()->state(),
            'uf' => strtoupper(fake()->lexify('??')),
            'country' => 'BR',
        ];
    }
}
