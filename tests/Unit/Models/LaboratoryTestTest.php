<?php

namespace Tests\Unit\Models;

use App\Models\LaboratoryTest;
use App\Models\LaboratoryOrder;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LaboratoryTestTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $order = LaboratoryOrder::create(['order_number' => 'LAB-001', 'pet_id' => $pet->id, 'vet_id' => $vet->id, 'order_date' => now(), 'status' => 'pending']);
        LaboratoryTest::create([
            'laboratory_order_id' => $order->id, 'test_name' => 'Hemograma',
            'test_code' => 'HEMO', 'result' => 'Normal', 'is_abnormal' => false,
        ]);
        $this->assertDatabaseHas('laboratory_tests', ['laboratory_order_id' => $order->id, 'test_name' => 'Hemograma']);
    }

    public function test_laboratory_order_relationship()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $order = LaboratoryOrder::create(['order_number' => 'LAB-002', 'pet_id' => $pet->id, 'vet_id' => $vet->id, 'order_date' => now(), 'status' => 'pending']);
        $lt = LaboratoryTest::create(['laboratory_order_id' => $order->id, 'test_name' => 'Teste']);
        $this->assertInstanceOf(LaboratoryOrder::class, $lt->laboratoryOrder);
    }
}
