<?php

namespace Database\Factories;

use App\Models\CommissionLog;
use App\Models\User;
use App\Models\Invoice;
use App\Models\CommissionRate;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommissionLogFactory extends Factory
{
    protected $model = CommissionLog::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'invoice_id' => Invoice::factory(),
            'commission_rate_id' => CommissionRate::factory(),
            'description' => $this->faker->sentence(),
            'base_value' => $this->faker->randomFloat(2, 100, 1000),
            'commission_value' => $this->faker->randomFloat(2, 10, 200),
            'status' => 'pending',
        ];
    }
}
