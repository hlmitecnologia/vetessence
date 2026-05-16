<?php

namespace Database\Factories;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseOrderFactory extends Factory
{
    protected $model = PurchaseOrder::class;

    public function definition()
    {
        return [
            'order_number' => 'PO-' . $this->faker->unique()->numberBetween(1000, 9999) . '/' . now()->year,
            'supplier_id' => Supplier::factory(),
            'requested_by' => User::factory(),
            'status' => 'draft',
            'total' => $this->faker->randomFloat(2, 100, 10000),
        ];
    }
}
