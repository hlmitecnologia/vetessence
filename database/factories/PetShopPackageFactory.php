<?php

namespace Database\Factories;

use App\Models\PetShopPackage;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class PetShopPackageFactory extends Factory
{
    protected $model = PetShopPackage::class;

    public function definition()
    {
        return [
            'branch_id' => Branch::factory(),
            'name' => $this->faker->word(),
            'type' => 'grooming',
            'services' => json_encode([['service_id' => 1, 'qty' => 1]]),
            'total_price' => $this->faker->randomFloat(2, 50, 500),
            'original_price' => $this->faker->randomFloat(2, 100, 600),
            'validity_days' => 30,
            'max_uses' => 5,
            'is_active' => true,
        ];
    }
}
