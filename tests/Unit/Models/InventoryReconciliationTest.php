<?php

namespace Tests\Unit\Models;

use App\Models\InventoryReconciliation;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class InventoryReconciliationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $product = Product::factory()->create();
        $user = User::factory()->create();
        $record = InventoryReconciliation::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'expected_quantity' => 50,
            'actual_quantity' => 48,
            'variance' => -2,
            'type' => 'manual',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('inventory_reconciliations', [
            'product_id' => $product->id,
            'variance' => -2,
        ]);
    }

    public function test_product_relationship()
    {
        $product = Product::factory()->create();
        $reconciliation = InventoryReconciliation::factory()->create([
            'product_id' => $product->id,
        ]);

        $this->assertInstanceOf(Product::class, $reconciliation->product);
    }

    public function test_reconciled_scope()
    {
        InventoryReconciliation::factory()->count(3)->create(['status' => 'pending']);
        InventoryReconciliation::factory()->count(2)->reconciled()->create();

        $this->assertEquals(2, InventoryReconciliation::where('status', 'reconciled')->count());
    }
}
