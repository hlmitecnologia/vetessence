<?php

namespace Tests\Unit\Models;

use App\Models\Branch;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PurchaseOrderTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $supplier = Supplier::factory()->create();
        $requester = User::factory()->create();
        $order = PurchaseOrder::create([
            'order_number' => 'PO-0001/2026',
            'supplier_id' => $supplier->id,
            'status' => 'draft',
            'requested_by' => $requester->id,
            'total' => 1500.00,
            'notes' => 'Urgente',
        ]);

        $this->assertDatabaseHas('purchase_orders', [
            'order_number' => 'PO-0001/2026',
            'notes' => 'Urgente',
        ]);
    }

    public function test_supplier_relationship()
    {
        $supplier = Supplier::factory()->create();
        $order = PurchaseOrder::factory()->create(['supplier_id' => $supplier->id]);
        $this->assertInstanceOf(Supplier::class, $order->supplier);
    }

    public function test_requester_relationship()
    {
        $user = User::factory()->create();
        $order = PurchaseOrder::factory()->create(['requested_by' => $user->id]);
        $this->assertInstanceOf(User::class, $order->requester);
    }

    public function test_total_cast()
    {
        $order = PurchaseOrder::factory()->create(['total' => 99.99]);
        $this->assertNotNull($order->total);
    }

    public function test_scopes()
    {
        PurchaseOrder::factory()->create(['status' => 'draft']);
        PurchaseOrder::factory()->create(['status' => 'ordered']);
        PurchaseOrder::factory()->create(['status' => 'received']);

        $this->assertEquals(1, PurchaseOrder::draft()->count());
        $this->assertEquals(1, PurchaseOrder::ordered()->count());
        $this->assertEquals(2, PurchaseOrder::pending()->count());
    }

    public function test_generates_order_number()
    {
        $number = PurchaseOrder::generateNumber();
        $this->assertStringContainsString('PO-', $number);
    }
}
