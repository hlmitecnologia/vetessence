<?php

namespace Database\Factories;

use App\Models\BankAccount;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class BankAccountFactory extends Factory
{
    protected $model = BankAccount::class;

    public function definition()
    {
        return [
            'branch_id' => Branch::factory(),
            'bank' => 'Banco do Brasil',
            'agency' => $this->faker->numerify('####-#'),
            'account' => $this->faker->numerify('######-#'),
            'account_type' => 'checking',
            'is_active' => true,
        ];
    }
}
