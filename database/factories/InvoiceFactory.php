<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Invoice;
use App\Models\Tutor;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition()
    {
        $total = $this->faker->randomFloat(2, 50, 500);
        return [
            'invoice_number' => Invoice::generateNumber(),
            'tutor_id' => Tutor::factory(),
            'subtotal' => $total,
            'total' => $total,
            'status' => $this->faker->randomElement(['pending', 'paid', 'cancelled']),
            'due_date' => now()->addDays(30),
            'branch_id' => Branch::factory(),
        ];
    }
}
