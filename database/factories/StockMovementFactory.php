<?php

namespace Database\Factories;

use App\Models\StockMovement;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockMovementFactory extends Factory
{
    protected $model = StockMovement::class;

    public function definition()
    {
        return [
            'product_id' => Product::factory(),
            'type' => 'exit',
            'quantity' => $this->faker->numberBetween(1, 100),
            'user_id' => User::factory(),
            'created_at' => now(),
        ];
    }
}
