<?php

namespace Tests\Feature\Controllers;

use App\Models\Branch;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\User;
use Tests\ModuleTestCase;

class PurchaseOrderControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index()
    {
        PurchaseOrder::factory()->count(3)->create();
        $response = $this->get(route('purchase-orders.index'));
        $response->assertOk();
    }

    public function test_create()
    {
        Branch::factory()->create();
        Supplier::factory()->create();
        Product::factory()->create();
        $response = $this->get(route('purchase-orders.create'));
        $response->assertOk();
    }

    public function test_store_creates_order()
    {
        $supplier = Supplier::factory()->create();
        $product = Product::factory()->create();
        $branch = Branch::factory()->create();

        $response = $this->post(route('purchase-orders.store'), [
            'supplier_id' => $supplier->id,
            'branch_id' => $branch->id,
            'notes' => 'Pedido urgente',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 10, 'unit_price' => 25.50],
            ],
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('purchase_orders', [
            'supplier_id' => $supplier->id,
            'status' => 'draft',
        ]);
    }

    public function test_store_validates_items_required()
    {
        $response = $this->post(route('purchase-orders.store'), [
            'supplier_id' => 1,
        ]);
        $response->assertSessionHasErrors(['items']);
    }

    public function test_store_validates_items_min_count()
    {
        $response = $this->post(route('purchase-orders.store'), [
            'supplier_id' => 1,
            'items' => [],
        ]);
        $response->assertSessionHasErrors('items');
    }

    public function test_show()
    {
        $order = PurchaseOrder::factory()->create();
        $response = $this->get(route('purchase-orders.show', $order));
        $response->assertOk();
    }

    public function test_edit()
    {
        $order = PurchaseOrder::factory()->create(['status' => 'draft']);
        Branch::factory()->create();
        Supplier::factory()->create();
        Product::factory()->create();
        $response = $this->get(route('purchase-orders.edit', $order));
        $response->assertOk();
    }

    public function test_edit_fails_for_non_draft()
    {
        $order = PurchaseOrder::factory()->create(['status' => 'ordered']);
        $response = $this->get(route('purchase-orders.edit', $order));
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_update_modifies_record()
    {
        $order = PurchaseOrder::factory()->create(['status' => 'draft']);
        $supplier = Supplier::factory()->create();
        $product = Product::factory()->create();

        $response = $this->put(route('purchase-orders.update', $order), [
            'supplier_id' => $supplier->id,
            'notes' => 'Notas atualizadas',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 5, 'unit_price' => 30.00],
            ],
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('purchase_orders', [
            'id' => $order->id,
            'supplier_id' => $supplier->id,
        ]);
    }

    public function test_update_fails_for_non_draft()
    {
        $order = PurchaseOrder::factory()->create(['status' => 'ordered']);
        $response = $this->put(route('purchase-orders.update', $order), [
            'supplier_id' => $order->supplier_id,
            'items' => [
                ['product_id' => 1, 'quantity' => 1, 'unit_price' => 10],
            ],
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_destroy_deletes_draft()
    {
        $order = PurchaseOrder::factory()->create(['status' => 'draft']);
        PurchaseOrderItem::factory()->create(['purchase_order_id' => $order->id]);

        $response = $this->delete(route('purchase-orders.destroy', $order));
        $response->assertRedirect(route('purchase-orders.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('purchase_orders', ['id' => $order->id]);
    }

    public function test_destroy_fails_for_non_draft()
    {
        $order = PurchaseOrder::factory()->create(['status' => 'ordered']);
        $response = $this->delete(route('purchase-orders.destroy', $order));
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('purchase_orders', ['id' => $order->id]);
    }

    public function test_order_changes_status()
    {
        $order = PurchaseOrder::factory()->create(['status' => 'draft']);
        $response = $this->post(route('purchase-orders.order', $order));
        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('purchase_orders', [
            'id' => $order->id,
            'status' => 'ordered',
        ]);
    }

    public function test_order_fails_if_already_ordered()
    {
        $order = PurchaseOrder::factory()->create(['status' => 'ordered']);
        $response = $this->post(route('purchase-orders.order', $order));
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_receive()
    {
        $product = Product::factory()->create();
        $order = PurchaseOrder::factory()->create(['status' => 'ordered']);
        $item = PurchaseOrderItem::factory()->create([
            'purchase_order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 10,
            'unit_price' => 25.00,
        ]);

        $response = $this->post(route('purchase-orders.receive', $order), [
            'items' => [
                ['id' => $item->id, 'received_quantity' => 10],
            ],
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('purchase_orders', [
            'id' => $order->id,
            'status' => 'received',
        ]);
    }

    public function test_receive_fails_if_not_ordered()
    {
        $order = PurchaseOrder::factory()->create(['status' => 'draft']);
        $response = $this->post(route('purchase-orders.receive', $order), [
            'items' => [],
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }
}
