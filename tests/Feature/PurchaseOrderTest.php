<?php

namespace Tests\Feature;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\Product;
use Tests\ModuleTestCase;

class PurchaseOrderTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('estoque');
    }

    public function test_index_page_loads()
    {
        PurchaseOrder::factory()->count(3)->create();
        $response = $this->get(route('purchase-orders.index'));
        $response->assertStatus(200);
    }

    public function test_create_page_loads()
    {
        $response = $this->get(route('purchase-orders.create'));
        $response->assertStatus(200);
    }

    public function test_store_creates_order()
    {
        $supplier = Supplier::factory()->create();
        $product = Product::factory()->create();

        $response = $this->post(route('purchase-orders.store'), [
            'supplier_id' => $supplier->id,
            'notes' => 'Urgente',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 10, 'unit_price' => 25.50],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('purchase_orders', [
            'supplier_id' => $supplier->id,
            'status' => 'draft',
            'total' => 255.00,
        ]);
        $this->assertDatabaseHas('purchase_order_items', [
            'product_id' => $product->id,
            'quantity' => 10,
            'unit_price' => 25.50,
        ]);
    }

    public function test_order_transitions_to_ordered()
    {
        $order = PurchaseOrder::factory()->create(['status' => 'draft']);
        $response = $this->post(route('purchase-orders.order', $order));
        $response->assertRedirect();
        $this->assertDatabaseHas('purchase_orders', [
            'id' => $order->id,
            'status' => 'ordered',
        ]);
        $this->assertNotNull($order->fresh()->ordered_at);
    }

    public function test_receive_transitions_to_received()
    {
        $order = PurchaseOrder::factory()->create(['status' => 'ordered']);
        $item = PurchaseOrderItem::factory()->create([
            'purchase_order_id' => $order->id,
            'quantity' => 10,
            'received_quantity' => 0,
        ]);

        $response = $this->post(route('purchase-orders.receive', $order), [
            'items' => [
                ['id' => $item->id, 'received_quantity' => 8],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('purchase_orders', [
            'id' => $order->id,
            'status' => 'received',
        ]);
        $this->assertDatabaseHas('purchase_order_items', [
            'id' => $item->id,
            'received_quantity' => 8,
        ]);
    }

    public function test_cannot_edit_ordered_order()
    {
        $order = PurchaseOrder::factory()->create(['status' => 'ordered']);
        $response = $this->get(route('purchase-orders.edit', $order));
        $response->assertRedirect();
    }

    public function test_cannot_delete_ordered_order()
    {
        $order = PurchaseOrder::factory()->create(['status' => 'ordered']);
        $response = $this->delete(route('purchase-orders.destroy', $order));
        $response->assertRedirect();
        $this->assertDatabaseHas('purchase_orders', ['id' => $order->id]);
    }

    public function test_generates_order_number()
    {
        $number = PurchaseOrder::generateNumber();
        $this->assertStringContainsString('PO-', $number);
    }
}
