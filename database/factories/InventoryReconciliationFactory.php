<?php

namespace Database\Factories;

use App\Models\InventoryReconciliation;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryReconciliationFactory extends Factory
{
    protected $model = InventoryReconciliation::class;

    public function definition()
    {
        $expected = $this->faker->numberBetween(1, 100);
        $actual = $expected + $this->faker->numberBetween(-5, 5);

        return [
            'product_id' => Product::factory(),
            'user_id' => User::factory(),
            'expected_quantity' => $expected,
            'actual_quantity' => $actual,
            'variance' => $actual - $expected,
            'type' => 'manual',
            'status' => 'pending',
        ];
    }

    public function reconciled()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'reconciled',
                'reconciled_at' => now(),
                'approved_by' => User::factory(),
            ];
        });
    }
}
