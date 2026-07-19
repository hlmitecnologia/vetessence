<?php

namespace Tests\Unit\Services;

use App\Models\Branch;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Services\StockForecastService;
use Tests\ModuleTestCase;

class StockForecastServiceTest extends ModuleTestCase
{
    private StockForecastService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(StockForecastService::class);
    }

    public function test_calculate_avg_daily_consumption_returns_zero_when_no_movements()
    {
        $product = Product::factory()->create();
        $avg = $this->service->calculateAvgDailyConsumption($product);

        $this->assertEquals(0, $avg);
    }

    public function test_calculate_avg_daily_consumption_with_movements()
    {
        $product = Product::factory()->create();
        StockMovement::factory()->create([
            'product_id' => $product->id,
            'type' => 'exit',
            'quantity' => 10,
            'created_at' => now()->subDays(5),
        ]);
        StockMovement::factory()->create([
            'product_id' => $product->id,
            'type' => 'exit',
            'quantity' => 20,
            'created_at' => now()->subDays(10),
        ]);

        $avg = $this->service->calculateAvgDailyConsumption($product, 30);

        $this->assertEqualsWithDelta(1.0, $avg, 0.01);
    }

    public function test_update_reorder_point_calculates_correctly()
    {
        $supplier = Supplier::factory()->create(['lead_time_days' => 5]);
        $product = Product::factory()->create([
            'supplier_id' => $supplier->id,
            'safety_stock' => 10,
        ]);

        StockMovement::factory()->count(10)->create([
            'product_id' => $product->id,
            'type' => 'exit',
            'quantity' => 3,
            'created_at' => now()->subDays(1),
        ]);

        $this->service->updateReorderPoint($product);

        $product->refresh();
        $this->assertGreaterThan(0, $product->avg_daily_consumption);
        $this->assertGreaterThan(0, $product->reorder_point);
        $this->assertNotNull($product->last_consumption_calculated_at);
    }

    public function test_suggest_purchase_order_returns_empty_when_no_products_below_reorder()
    {
        $product = Product::factory()->create([
            'reorder_point' => 10,
            'stock' => 20,
            'is_active' => true,
        ]);

        $suggestions = $this->service->suggestPurchaseOrder();

        $this->assertCount(0, $suggestions);
    }

    public function test_suggest_purchase_order_returns_suggestion_when_below_reorder()
    {
        $supplier = Supplier::factory()->create();
        $product = Product::factory()->create([
            'supplier_id' => $supplier->id,
            'reorder_point' => 20,
            'stock' => 5,
            'is_active' => true,
        ]);

        $suggestions = $this->service->suggestPurchaseOrder();

        $this->assertCount(1, $suggestions);
        $this->assertEquals($product->id, $suggestions[0]->product->id);
        $this->assertEquals(15, $suggestions[0]->suggested_quantity);
    }

    public function test_expiring_products_returns_products_expiring_soon()
    {
        $product = Product::factory()->create([
            'expiration_date' => now()->addDays(10),
            'stock' => 50,
            'cost_price' => 10,
            'is_active' => true,
        ]);

        $expiring = $this->service->expiringProducts(30);

        $this->assertCount(1, $expiring);
        $this->assertLessThanOrEqual(10, $expiring[0]->days_to_expiry);
        $this->assertEquals(500, $expiring[0]->total_value);
    }

    public function test_expiring_products_excludes_expired()
    {
        Product::factory()->create([
            'expiration_date' => now()->subDays(5),
            'is_active' => true,
        ]);

        $expiring = $this->service->expiringProducts(30);

        $this->assertCount(0, $expiring);
    }

    public function test_recalculate_all_updates_all_active_products()
    {
        $supplier = Supplier::factory()->create(['lead_time_days' => 3]);
        Product::factory()->count(3)->create([
            'is_active' => true,
            'supplier_id' => $supplier->id,
        ]);

        $result = $this->service->recalculateAll();

        $this->assertGreaterThanOrEqual(3, $result['updated']);
    }

    public function test_suggest_purchase_order_filters_by_branch()
    {
        $branch = Branch::factory()->create();
        $product = Product::factory()->create([
            'reorder_point' => 10,
            'stock' => 2,
            'is_active' => true,
        ]);
        StockMovement::factory()->create([
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'type' => 'exit',
            'quantity' => 1,
        ]);

        $suggestions = $this->service->suggestPurchaseOrder($branch);

        $this->assertCount(1, $suggestions);
    }

    public function test_expiring_products_filters_by_branch()
    {
        $branch = Branch::factory()->create();
        $product = Product::factory()->create([
            'expiration_date' => now()->addDays(5),
            'stock' => 10,
            'is_active' => true,
        ]);
        StockMovement::factory()->create([
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'type' => 'exit',
            'quantity' => 1,
        ]);

        $expiring = $this->service->expiringProducts(30, $branch);

        $this->assertCount(1, $expiring);
    }
}
