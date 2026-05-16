<?php

namespace Tests\Unit\Models;

use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        Product::create([
            'name' => 'Product A',
            'sku' => 'SKU001',
            'cost_price' => 50.00,
            'sale_price' => 100.00,
            'stock' => 10,
            'min_stock' => 5,
            'is_active' => true,
            'batch_number' => 'BATCH-001',
            'lot_number' => 'LOT-2024-A',
        ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Product A',
            'sku' => 'SKU001',
            'batch_number' => 'BATCH-001',
            'lot_number' => 'LOT-2024-A',
        ]);
    }

    public function test_expiration_date_cast()
    {
        $product = Product::factory()->create(['expiration_date' => '2025-06-01']);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $product->expiration_date);
    }

    public function test_isLowStock_accessor_returns_true_when_stock_below_min()
    {
        $product = Product::create([
            'name' => 'Product B',
            'sku' => 'SKU002',
            'cost_price' => 50.00,
            'sale_price' => 100.00,
            'stock' => 5,
            'min_stock' => 10,
            'is_active' => true,
        ]);

        $this->assertTrue($product->isLowStock);
    }

    public function test_isLowStock_accessor_returns_false_when_stock_above_min()
    {
        $product = Product::create([
            'name' => 'Product C',
            'sku' => 'SKU003',
            'cost_price' => 50.00,
            'sale_price' => 100.00,
            'stock' => 15,
            'min_stock' => 10,
            'is_active' => true,
        ]);

        $this->assertFalse($product->isLowStock);
    }

    public function test_margin_accessor()
    {
        $product = Product::create([
            'name' => 'Product D',
            'sku' => 'SKU004',
            'cost_price' => 50.00,
            'sale_price' => 100.00,
            'stock' => 10,
            'min_stock' => 5,
            'is_active' => true,
        ]);

        $this->assertEquals(100.0, $product->margin);
    }
}
