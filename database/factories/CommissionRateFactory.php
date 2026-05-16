<?php

namespace Database\Factories;

use App\Models\CommissionRate;
use App\Models\User;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommissionRateFactory extends Factory
{
    protected $model = CommissionRate::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'commissionable_type' => Service::class,
            'commissionable_id' => Service::factory(),
            'rate_type' => 'percentage',
            'rate_value' => 10.00,
            'is_active' => true,
        ];
    }
}
