<?php

namespace Tests\Feature\Commands;

use App\Models\Branch;
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

    public function test_confirmed_csv_creates_active_batch_and_is_idempotent(): void
    {
        $branch = Branch::factory()->create();
        $product = Product::factory()->create([
            'stock' => 4.5,
            'stock_unit' => 'ml',
            'unit' => 'ml',
            'package_quantity' => 10,
        ]);
        $file = $this->createReconciliationCsv([
            [$product->id, $branch->id, '4.5', 'ml', now()->addDays(30)->toDateString()],
        ]);

        try {
            Artisan::call('inventory:reconcile-legacy', [
                '--confirm-file' => $file,
                '--apply' => true,
                '--json' => true,
            ]);
            Artisan::call('inventory:reconcile-legacy', [
                '--confirm-file' => $file,
                '--apply' => true,
                '--json' => true,
            ]);
        } finally {
            @unlink($file);
        }

        $this->assertDatabaseHas('inventory_batches', [
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'quantity_available' => 4.5,
            'unit' => 'ml',
            'status' => 'active',
            'is_legacy' => true,
        ]);
        $this->assertSame(1, InventoryBatch::withoutGlobalScopes()
            ->where('product_id', $product->id)
            ->where('branch_id', $branch->id)
            ->where('is_legacy', true)
            ->count());
    }

    public function test_confirmed_csv_without_expiration_remains_quarantined(): void
    {
        $branch = Branch::factory()->create();
        $product = Product::factory()->create(['stock' => 2, 'stock_unit' => 'ml', 'unit' => 'ml']);
        $file = $this->createReconciliationCsv([[$product->id, $branch->id, '2', 'ml', '']]);

        try {
            Artisan::call('inventory:reconcile-legacy', [
                '--confirm-file' => $file,
                '--apply' => true,
            ]);
        } finally {
            @unlink($file);
        }

        $this->assertDatabaseHas('inventory_batches', [
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'status' => 'quarantined',
        ]);
    }

    public function test_csv_reports_unknown_product_without_writing(): void
    {
        $branch = Branch::factory()->create();
        $file = $this->createReconciliationCsv([[999999999, $branch->id, '2', 'ml', now()->addDays(30)->toDateString()]]);

        try {
            Artisan::call('inventory:reconcile-legacy', [
                '--confirm-file' => $file,
                '--apply' => true,
                '--json' => true,
            ]);
            $output = Artisan::output();
        } finally {
            @unlink($file);
        }

        $this->assertStringContainsString('product_not_found', $output);
        $this->assertDatabaseCount('inventory_batches', 0);
    }

    public function test_json_output_is_machine_readable(): void
    {
        $branch = Branch::factory()->create();
        $file = $this->createReconciliationCsv([[999999999, $branch->id, '2', 'ml', now()->addDays(30)->toDateString()]]);

        try {
            Artisan::call('inventory:reconcile-legacy', [
                '--confirm-file' => $file,
                '--json' => true,
            ]);
            $decoded = json_decode(Artisan::output(), true);
        } finally {
            @unlink($file);
        }

        $this->assertIsArray($decoded);
        $this->assertSame('invalid', $decoded[0]['status']);
    }

    private function createReconciliationCsv(array $rows): string
    {
        $directory = storage_path('framework/testing');
        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }
        $file = tempnam($directory, 'legacy-reconciliation-');
        file_put_contents($file, "product_id,branch_id,quantity,unit,expiration_date\n");
        foreach ($rows as $row) {
            file_put_contents($file, implode(',', $row) . "\n", FILE_APPEND);
        }
        return $file;
    }
}
