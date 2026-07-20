<?php

namespace Database\Factories;

use App\Models\PaymentGateway;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentGatewayFactory extends Factory
{
    protected $model = PaymentGateway::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company . ' Gateway',
            'provider' => $this->faker->randomElement(['mercadopago', 'pix', 'stripe']),
            'channel' => 'portal',
            'is_active' => false,
            'is_sandbox' => true,
        ];
    }
}
