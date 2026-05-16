<?php

namespace Tests\Unit\Models;

use App\Models\StockMovement;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StockMovementTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $user = User::factory()->create();
        $product = Product::create(['name' => 'Ração Premier', 'sku' => 'SKU-001', 'stock' => 100, 'cost_price' => 10.00, 'sale_price' => 25.00]);
        StockMovement::create([
            'product_id' => $product->id, 'type' => 'in', 'quantity' => 50,
            'batch_number' => 'BATCH-001', 'lot_number' => 'LOT-2024-A',
            'expiry_date' => '2025-12-31', 'balance_after' => 150,
            'notes' => 'Compra', 'user_id' => $user->id, 'created_at' => now(),
        ]);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id, 'batch_number' => 'BATCH-001',
            'lot_number' => 'LOT-2024-A',
        ]);
    }

    public function test_expiry_date_cast()
    {
        $user = User::factory()->create();
        $product = Product::create(['name' => 'Teste', 'sku' => 'SKU-X', 'stock' => 5, 'cost_price' => 5.00, 'sale_price' => 15.00]);
        $sm = StockMovement::create([
            'product_id' => $product->id, 'type' => 'in', 'quantity' => 10,
            'expiry_date' => '2025-06-01', 'user_id' => $user->id, 'balance_after' => 15,
        ]);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $sm->expiry_date);
    }

    public function test_product_relationship()
    {
        $user = User::factory()->create();
        $product = Product::create(['name' => 'Teste', 'sku' => 'SKU-002', 'stock' => 10, 'cost_price' => 5.00, 'sale_price' => 15.00]);
        $sm = StockMovement::create(['product_id' => $product->id, 'type' => 'in', 'quantity' => 10, 'user_id' => $user->id, 'balance_after' => 20]);
        $this->assertInstanceOf(Product::class, $sm->product);
    }
}
