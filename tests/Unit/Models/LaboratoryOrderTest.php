<?php

namespace Tests\Unit\Models;

use App\Models\LaboratoryOrder;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LaboratoryOrderTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        LaboratoryOrder::create([
            'order_number' => 'LAB-20260515-0001', 'pet_id' => $pet->id, 'vet_id' => $vet->id,
            'lab_name' => 'Lab Teste', 'order_date' => now(), 'status' => 'pending',
        ]);
        $this->assertDatabaseHas('laboratory_orders', ['order_number' => 'LAB-20260515-0001', 'status' => 'pending']);
    }

    public function test_pet_relationship()
    {
        $pet = Pet::factory()->create();
        $lo = LaboratoryOrder::create(['order_number' => 'LAB-001', 'pet_id' => $pet->id, 'order_date' => now(), 'status' => 'pending']);
        $this->assertInstanceOf(Pet::class, $lo->pet);
    }

    public function test_vet_relationship()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $lo = LaboratoryOrder::create(['order_number' => 'LAB-002', 'pet_id' => $pet->id, 'vet_id' => $vet->id, 'order_date' => now(), 'status' => 'pending']);
        $this->assertInstanceOf(User::class, $lo->vet);
    }
}
