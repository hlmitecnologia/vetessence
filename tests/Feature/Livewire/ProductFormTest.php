<?php

namespace Tests\Feature\Livewire;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Livewire\Livewire;
use Tests\ModuleTestCase;

class ProductFormTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_can_create_product_with_required_fields()
    {
        Livewire::test('product-form')
            ->set('name', 'Vacina V10')
            ->set('sku', 'VAC-001')
            ->set('cost_price', '25.00')
            ->set('sale_price', '90.00')
            ->set('stock', '50')
            ->call('save')
            ->assertDispatched('product-saved')
            ->assertDispatched('close-modal');

        $this->assertDatabaseHas('products', [
            'name' => 'Vacina V10',
            'cost_price' => 25.00,
            'sale_price' => 90.00,
            'stock' => 50,
        ]);
    }

    public function test_validates_required_fields()
    {
        Livewire::test('product-form')
            ->call('save')
            ->assertHasErrors(['name', 'sku', 'cost_price', 'sale_price', 'stock']);
    }

    public function test_can_edit_existing_product()
    {
        $product = Product::factory()->create([
            'name' => 'Produto Antigo',
            'cost_price' => 10.00,
            'sale_price' => 30.00,
            'stock' => 100,
        ]);

        Livewire::test('product-form', ['id' => $product->id])
            ->assertSet('name', 'Produto Antigo')
            ->set('name', 'Produto Novo')
            ->set('sale_price', '35.00')
            ->call('save')
            ->assertDispatched('product-saved');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Produto Novo',
            'sale_price' => 35.00,
        ]);
    }

    public function test_can_edit_product_via_event()
    {
        $product = Product::factory()->create([
            'name' => 'Event Product',
            'cost_price' => 15.00,
            'sale_price' => 45.00,
            'stock' => 20,
        ]);

        Livewire::test('product-form')
            ->dispatch('editProduct', id: $product->id)
            ->assertSet('productId', $product->id)
            ->assertSet('name', 'Event Product')
            ->set('name', 'Event Product Updated')
            ->call('save')
            ->assertDispatched('product-saved');

        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Event Product Updated']);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 20]);
    }

    public function test_stock_tracking_zero_stock()
    {
        Livewire::test('product-form')
            ->set('name', 'Produto Sem Estoque')
            ->set('sku', 'SEM-ESTOQUE-001')
            ->set('cost_price', '10.00')
            ->set('sale_price', '25.00')
            ->set('stock', '0')
            ->call('save')
            ->assertDispatched('product-saved');

        $this->assertDatabaseHas('products', ['name' => 'Produto Sem Estoque', 'stock' => 0]);
    }

    public function test_can_create_product_with_category_and_supplier()
    {
        $category = Category::factory()->create(['type' => 'product']);
        $supplier = Supplier::factory()->create();

        Livewire::test('product-form')
            ->set('name', 'Antibiótico')
            ->set('category_id', (string) $category->id)
            ->set('supplier_id', (string) $supplier->id)
            ->set('cost_price', '15.00')
            ->set('sale_price', '45.00')
            ->set('stock', '200')
            ->set('sku', 'ANTB-001')
            ->call('save')
            ->assertDispatched('product-saved');

        $this->assertDatabaseHas('products', [
            'name' => 'Antibiótico',
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'sku' => 'ANTB-001',
        ]);
    }

    public function test_validates_numeric_fields()
    {
        Livewire::test('product-form')
            ->set('name', 'Produto')
            ->set('sku', 'TEST-001')
            ->set('cost_price', '-5')
            ->set('sale_price', '-10')
            ->set('stock', '-1')
            ->call('save')
            ->assertHasErrors(['cost_price', 'sale_price', 'stock']);
    }

    public function test_reset_form_clears_properties()
    {
        $product = Product::factory()->create([
            'name' => 'Temp',
            'cost_price' => 10,
            'sale_price' => 20,
            'stock' => 5,
        ]);

        Livewire::test('product-form')
            ->dispatch('editProduct', id: $product->id)
            ->assertSet('productId', $product->id)
            ->dispatch('resetForm')
            ->assertSet('productId', null)
            ->assertSet('name', '')
            ->assertSet('cost_price', '')
            ->assertSet('stock', '')
            ->assertSet('is_active', true);
    }
}
