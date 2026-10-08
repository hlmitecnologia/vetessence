<?php

namespace Tests\Feature\Commands;

use App\Models\InventoryBatch;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ReconcileLegacyInventoryTest extends TestCase
{
    use DatabaseTransactions;

    public function test_report_does_not_write_and_apply_quarantines_unconfirmed_balance(): void
    {
        $product = Product::factory()->create([
            'stock' => 10,
            'stock_unit' => 'ml',
            'unit' => 'ml',
            'package_quantity' => 10,
        ]);

        Artisan::call('inventory:reconcile-legacy', ['--json' => true]);

        $this->assertDatabaseMissing('inventory_batches', [
            'product_id' => $product->id,
            'is_legacy' => true,
        ]);

        Artisan::call('inventory:reconcile-legacy', ['--apply' => true]);

        $this->assertDatabaseHas('inventory_batches', [
            'product_id' => $product->id,
            'batch_number' => 'LEGACY-SIN-LOTE',
            'quantity_available' => 10,
            'unit' => 'ml',
            'status' => 'quarantined',
            'is_legacy' => true,
        ]);
    }
}
