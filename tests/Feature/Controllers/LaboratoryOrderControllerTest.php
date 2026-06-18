<?php

namespace Tests\Feature\Controllers;

use App\Models\LaboratoryOrder;
use App\Models\Pet;
use App\Models\User;
use Tests\ModuleTestCase;

class LaboratoryOrderControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        $response = $this->get(route('laboratory-orders.index'));
        $response->assertOk();
    }

    public function test_store_creates_laboratory_order()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();

        $response = $this->post(route('laboratory-orders.store'), [
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'order_date' => now()->format('Y-m-d'),
            'lab_name' => 'Lab Veterinário',
            'status' => 'requested',
            'notes' => 'Solicito hemograma',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('laboratory_orders', [
            'pet_id' => $pet->id,
            'lab_name' => 'Lab Veterinário',
        ]);
    }

    public function test_show()
    {
        $order = LaboratoryOrder::factory()->create();

        $response = $this->get(route('laboratory-orders.show', $order));
        $response->assertOk();
    }

    public function test_update()
    {
        $order = LaboratoryOrder::factory()->create();

        $response = $this->put(route('laboratory-orders.update', $order), [
            'pet_id' => $order->pet_id,
            'vet_id' => $order->vet_id,
            'order_date' => now()->format('Y-m-d'),
            'status' => 'completed',
            'result_date' => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('laboratory_orders', [
            'id' => $order->id,
            'status' => 'completed',
        ]);
    }

    public function test_destroy()
    {
        $order = LaboratoryOrder::factory()->create();

        $response = $this->delete(route('laboratory-orders.destroy', $order));
        $response->assertRedirect();
        $this->assertDatabaseMissing('laboratory_orders', ['id' => $order->id]);
    }
}
