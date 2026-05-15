<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'sku' => $this->faker->unique()->ean8(),
            'stock' => 0,
            'cost_price' => 0,
            'sale_price' => 0,
            'is_active' => true,
        ];
    }
}
