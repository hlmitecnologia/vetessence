<?php

namespace Tests\Unit\Services;

use App\Models\InventoryBatch;
use App\Models\Product;
use App\Models\User;
use App\Services\FractionalInventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FractionalInventoryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create());
    }

    public function test_consuming_two_ml_from_a_ten_ml_container_leaves_eight_ml_and_records_movement(): void
    {
        $product = Product::factory()->create([
            'stock_unit' => 'ml',
            'dispensing_unit' => 'ml',
            'allows_fractional' => true,
            'requires_container_tracking' => true,
        ]);
        $batch = InventoryBatch::create([
            'product_id' => $product->id,
            'quantity_received' => 10,
            'quantity_available' => 10,
            'unit' => 'ml',
            'package_quantity' => 10,
            'package_count' => 1,
            'status' => 'active',
        ]);
        $container = $batch->containers()->create([
            'identifier' => 'FRASCO-1',
            'initial_quantity' => 10,
            'available_quantity' => 10,
            'unit' => 'ml',
            'status' => 'unopened',
        ]);

        app(FractionalInventoryService::class)->consume($product, 2, 'ml', [
            'batch_id' => $batch->id,
            'reference' => 'test-administration',
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'inventory_batch_id' => $batch->id,
            'inventory_container_id' => $container->id,
            'type' => 'out',
            'quantity' => 2,
            'unit' => 'ml',
        ]);
        $this->assertSame('8.0000', $container->fresh()->available_quantity);
        $this->assertSame('8.0000', $batch->fresh()->quantity_available);
    }

    public function test_consumption_is_rejected_for_product_not_marked_fractional(): void
    {
        $product = Product::factory()->create(['allows_fractional' => false]);

        $this->expectException(\InvalidArgumentException::class);
        app(FractionalInventoryService::class)->consume($product, 1, 'un');
    }
}
