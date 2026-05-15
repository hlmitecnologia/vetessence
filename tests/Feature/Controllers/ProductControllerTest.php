<?php

namespace Tests\Feature\Controllers;

use App\Models\Product;
use Tests\ModuleTestCase;

class ProductControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        Product::factory()->count(3)->create();
        $response = $this->get(route('products.index'));
        $response->assertOk();
    }

    public function test_create()
    {
        $response = $this->get(route('products.create'));
        $response->assertOk();
    }

    public function test_store_creates_record()
    {
        $response = $this->post(route('products.store'), [
            'name' => 'Ração Premium',
            'sku' => 'RACAO-001',
            'cost_price' => 50.00,
            'sale_price' => 89.90,
            'stock' => 100,
            'is_active' => true,
        ]);
        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('products', ['name' => 'Ração Premium', 'sku' => 'RACAO-001']);
    }

    public function test_store_validates_name()
    {
        $response = $this->post(route('products.store'), ['name' => '']);
        $response->assertSessionHasErrors('name');
    }

    public function test_show()
    {
        $product = Product::factory()->create();
        $response = $this->get(route('products.show', $product));
        $response->assertOk();
    }

    public function test_edit()
    {
        $product = Product::factory()->create();
        $response = $this->get(route('products.edit', $product));
        $response->assertOk();
    }

    public function test_update_modifies_record()
    {
        $product = Product::factory()->create();
        $response = $this->put(route('products.update', $product), [
            'name' => 'Ração Atualizada',
            'sku' => $product->sku,
            'cost_price' => 55.00,
            'sale_price' => 99.90,
            'stock' => 80,

        ]);
        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Ração Atualizada']);
    }

    public function test_destroy_deletes_record()
    {
        $product = Product::factory()->create(['stock' => 0]);
        $response = $this->delete(route('products.destroy', $product));
        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
