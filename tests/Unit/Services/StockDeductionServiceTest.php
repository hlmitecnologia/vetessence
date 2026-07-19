<?php

namespace Tests\Unit\Services;

use App\Models\Product;
use App\Models\Vaccination;
use App\Services\StockDeductionService;
use Tests\ModuleTestCase;

class StockDeductionServiceTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_deduct_creates_movement_and_reduces_stock()
    {
        $product = Product::factory()->create(['stock' => 5]);
        $vaccination = Vaccination::factory()->create();
        $vaccination->setAttribute('product_id', $product->id);
        $vaccination->save();

        $service = app(StockDeductionService::class);
        $service->deductFromVaccination($vaccination);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => 1,
        ]);
        $this->assertEquals(4, $product->fresh()->stock);
    }

    public function test_deduct_throws_when_insufficient_stock()
    {
        $product = Product::factory()->create(['stock' => 0]);
        $vaccination = Vaccination::factory()->create();
        $vaccination->setAttribute('product_id', $product->id);
        $vaccination->save();

        $service = app(StockDeductionService::class);

        $this->expectException(\RuntimeException::class);
        $service->deductFromVaccination($vaccination);
    }

    public function test_deduct_returns_early_when_no_product_id()
    {
        $countBefore = \DB::table('stock_movements')->count();
        $vaccination = Vaccination::factory()->create();

        app(StockDeductionService::class)->deductFromVaccination($vaccination);

        $this->assertEquals($countBefore, \DB::table('stock_movements')->count());
    }

    public function test_deduct_returns_early_when_product_not_found()
    {
        $countBefore = \DB::table('stock_movements')->count();
        $product = Product::factory()->create();
        $vaccination = Vaccination::factory()->create();
        $vaccination->setAttribute('product_id', $product->id);
        $vaccination->save();
        $product->delete();

        app(StockDeductionService::class)->deductFromVaccination($vaccination);

        $this->assertEquals($countBefore, \DB::table('stock_movements')->count());
    }
}
