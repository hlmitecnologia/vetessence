<?php

namespace Tests\Unit\Models;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PurchaseOrderItemTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $purchaseOrder = PurchaseOrder::factory()->create();
        $product = Product::factory()->create();
        PurchaseOrderItem::create([
            'purchase_order_id' => $purchaseOrder->id,
            'product_id' => $product->id,
            'quantity' => 10.00,
            'unit_price' => 25.50,
        ]);
        $this->assertDatabaseHas('purchase_order_items', [
            'purchase_order_id' => $purchaseOrder->id,
            'quantity' => 10.00,
        ]);
    }

    public function test_quantity_cast()
    {
        $item = PurchaseOrderItem::factory()->create(['quantity' => 5.50]);
        $this->assertEquals('5.50', $item->quantity);
    }

    public function test_purchase_order_relationship()
    {
        $purchaseOrder = PurchaseOrder::factory()->create();
        $item = PurchaseOrderItem::factory()->create(['purchase_order_id' => $purchaseOrder->id]);
        $this->assertTrue($item->purchaseOrder->is($purchaseOrder));
    }

    public function test_product_relationship()
    {
        $product = Product::factory()->create();
        $item = PurchaseOrderItem::factory()->create(['product_id' => $product->id]);
        $this->assertTrue($item->product->is($product));
    }
}
