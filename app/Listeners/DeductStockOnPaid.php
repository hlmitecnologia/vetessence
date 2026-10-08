<?php

namespace App\Listeners;

use App\Events\InvoicePaid;
use App\Models\InventoryBatch;
use App\Models\StockMovement;
use App\Services\FractionalInventoryService;
use Illuminate\Support\Facades\DB;

class DeductStockOnPaid
{
    public function __construct(?FractionalInventoryService $fractionalInventory = null)
    {
        $this->fractionalInventory = $fractionalInventory;
    }

    private ?FractionalInventoryService $fractionalInventory;

    public function handle(InvoicePaid $event): void
    {
        $invoice = $event->invoice;
        $this->fractionalInventory ??= app(FractionalInventoryService::class);
        $productItems = $invoice->items()->where('item_type', 'product')->with('product')->get();

        foreach ($productItems as $item) {
            if (!$item->product || (float) $item->quantity <= 0) {
                continue;
            }

            $unit = $item->unit ?: ($item->product->dispensing_unit ?: $item->product->stock_unit ?: 'un');
            $hasFractionalLedger = $item->product->allows_fractional
                && InventoryBatch::where('product_id', $item->product_id)->exists();

            if ($hasFractionalLedger) {
                $movement = $this->fractionalInventory->consume($item->product, (float) $item->quantity, $unit, [
                    'reference' => "invoice:{$item->invoice_id}:item:{$item->id}",
                    'reason' => $item->pricing_mode === 'fractional' ? 'fractional-sale' : 'sale',
                    'idempotency_key' => "invoice-paid:{$item->invoice_id}:item:{$item->id}",
                    'user_id' => $item->invoice->user_id,
                ]);
                $item->update([
                    'inventory_batch_id' => $movement->inventory_batch_id,
                    'inventory_container_id' => $movement->inventory_container_id,
                ]);
                continue;
            }

            if ((float) $item->product->stock < (float) $item->quantity) {
                continue;
            }
            DB::transaction(function () use ($item) {
                $item->product->decrement('stock', $item->quantity);
                StockMovement::create([
                    'product_id' => $item->product_id,
                    'branch_id' => $item->invoice->branch_id,
                    'user_id' => $item->invoice->user_id,
                    'type' => 'out',
                    'quantity' => $item->quantity,
                    'unit' => $item->unit,
                    'balance_after' => $item->product->fresh()->stock,
                    'notes' => "Venda - Fatura #{$item->invoice_id}",
                ]);
            });
        }
    }
}
