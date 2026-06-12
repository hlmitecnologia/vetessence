<?php

namespace Tests\Unit\Listeners;

use App\Events\InvoicePaid;
use App\Listeners\DeductStockOnPaid;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\User;
use Tests\ModuleTestCase;

class DeductStockOnPaidTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_deducts_stock_for_product_items(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);
        $invoice = Invoice::factory()->create(['user_id' => $user->id]);
        $invoice->items()->create([
            'description' => 'Produto',
            'quantity' => 2,
            'unit_price' => 50,
            'total' => 100,
            'item_type' => 'product',
            'product_id' => $product->id,
            'branch_id' => $invoice->branch_id,
        ]);

        $listener = new DeductStockOnPaid();
        $listener->handle(new InvoicePaid($invoice));

        $this->assertEquals(8, $product->fresh()->stock);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => 2,
            'branch_id' => $invoice->branch_id,
        ]);
    }

    public function test_skips_for_service_only_invoices(): void
    {
        $product = Product::factory()->create(['stock' => 10]);
        $invoice = Invoice::factory()->create();
        $invoice->items()->create([
            'description' => 'Serviço',
            'quantity' => 1,
            'unit_price' => 100,
            'total' => 100,
            'item_type' => 'service',
            'branch_id' => $invoice->branch_id,
        ]);

        $listener = new DeductStockOnPaid();
        $listener->handle(new InvoicePaid($invoice));

        $this->assertEquals(10, $product->fresh()->stock);
        $this->assertDatabaseCount('stock_movements', 0);
    }

    public function test_skips_items_without_product(): void
    {
        $invoice = Invoice::factory()->create();
        $invoice->items()->create([
            'description' => 'Produto sem vinculo',
            'quantity' => 1,
            'unit_price' => 50,
            'total' => 50,
            'item_type' => 'product',
            'product_id' => null,
            'branch_id' => $invoice->branch_id,
        ]);

        $listener = new DeductStockOnPaid();
        $listener->handle(new InvoicePaid($invoice));

        $this->assertDatabaseCount('stock_movements', 0);
    }

    public function test_skips_items_with_zero_stock(): void
    {
        $product = Product::factory()->create(['stock' => 0]);
        $invoice = Invoice::factory()->create();
        $invoice->items()->create([
            'description' => 'Produto sem estoque',
            'quantity' => 1,
            'unit_price' => 50,
            'total' => 50,
            'item_type' => 'product',
            'product_id' => $product->id,
            'branch_id' => $invoice->branch_id,
        ]);

        $listener = new DeductStockOnPaid();
        $listener->handle(new InvoicePaid($invoice));

        $this->assertEquals(0, $product->fresh()->stock);
        $this->assertDatabaseCount('stock_movements', 0);
    }
}
