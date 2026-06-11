<?php

namespace Tests\Feature\Controllers;

use App\Models\Supplier;
use Tests\ModuleTestCase;

class SupplierControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        Supplier::factory()->count(3)->create();
        $response = $this->get(route('suppliers.index'));
        $response->assertOk();
    }

    public function test_create()
    {
        $response = $this->get(route('suppliers.create'));
        $response->assertRedirect(route('suppliers.index'));
    }

    public function test_store_creates_record()
    {
        $response = $this->post(route('suppliers.store'), [
            'name' => 'Distribuidora ABC',
            'phone' => '11999999999',
            'email' => 'contato@abc.com',
            'address' => 'Rua Industrial, 100',
        ]);
        $response->assertRedirect(route('suppliers.index'));
        $this->assertDatabaseHas('suppliers', ['name' => 'Distribuidora ABC']);
    }

    public function test_store_validates_name()
    {
        $response = $this->post(route('suppliers.store'), ['name' => '']);
        $response->assertSessionHasErrors('name');
    }

    public function test_show()
    {
        $supplier = Supplier::factory()->create();
        $response = $this->get(route('suppliers.show', $supplier));
        $response->assertOk();
    }

    public function test_edit()
    {
        $supplier = Supplier::factory()->create();
        $response = $this->get(route('suppliers.edit', $supplier));
        $response->assertRedirect(route('suppliers.index'));
    }

    public function test_update_modifies_record()
    {
        $supplier = Supplier::factory()->create(['name' => 'Antigo']);
        $response = $this->put(route('suppliers.update', $supplier), [
            'name' => 'Novo Distribuidor',
        ]);
        $response->assertRedirect(route('suppliers.index'));
        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id, 'name' => 'Novo Distribuidor']);
    }

    public function test_destroy_deletes_record()
    {
        $supplier = Supplier::factory()->create();
        $response = $this->delete(route('suppliers.destroy', $supplier));
        $response->assertRedirect(route('suppliers.index'));
        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }
}
