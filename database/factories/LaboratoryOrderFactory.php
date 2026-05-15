<?php

namespace Database\Factories;

use App\Models\LaboratoryOrder;
use App\Models\Pet;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class LaboratoryOrderFactory extends Factory
{
    protected $model = LaboratoryOrder::class;

    public function definition()
    {
        return [
            'order_number' => 'LAB-' . date('Ymd') . '-' . str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT),
            'pet_id' => Pet::factory(),
            'vet_id' => User::factory(),
            'order_date' => $this->faker->date(),
            'status' => 'requested',
            'notes' => $this->faker->sentence(),
            'branch_id' => Branch::factory(),
        ];
    }
}
