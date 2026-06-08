<?php

namespace App\Listeners;

use App\Events\InvoicePaid;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class DeductStockOnPaid
{
    public function handle(InvoicePaid $event): void
    {
        $invoice = $event->invoice;

        $productItems = $invoice->items()->where('item_type', 'product')->with('product')->get();

        foreach ($productItems as $item) {
            if (!$item->product || $item->product->stock <= 0) {
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
                    'unit_price' => $item->unit_price,
                    'total' => $item->total,
                    'description' => "Venda - Fatura #{$item->invoice_id}",
                    'reference_type' => 'invoice',
                    'reference_id' => $item->invoice_id,
                ]);
            });
        }
    }
}
