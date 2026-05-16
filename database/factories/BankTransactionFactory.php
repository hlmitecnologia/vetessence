<?php

namespace Database\Factories;

use App\Models\BankTransaction;
use App\Models\BankAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class BankTransactionFactory extends Factory
{
    protected $model = BankTransaction::class;

    public function definition()
    {
        return [
            'bank_account_id' => BankAccount::factory(),
            'external_id' => $this->faker->uuid(),
            'description' => $this->faker->sentence(3),
            'amount' => $this->faker->randomFloat(2, 10, 5000),
            'transaction_date' => $this->faker->dateTimeBetween('-3 months'),
            'type' => $this->faker->randomElement(['credit', 'debit']),
            'status' => 'pending',
        ];
    }
}
