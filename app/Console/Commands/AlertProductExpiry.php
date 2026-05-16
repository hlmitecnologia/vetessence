<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\NotificationLog;
use Illuminate\Console\Command;

class AlertProductExpiry extends Command
{
    protected $signature = 'products:alert-expiry {--days=30 : Days threshold for expiry warning}';
    protected $description = 'Alert about products nearing or past expiry date';

    public function handle()
    {
        $days = (int) $this->option('days');
        $threshold = now()->addDays($days);
        $count = 0;

        $products = Product::whereNotNull('expiration_date')
            ->where('expiration_date', '<=', $threshold)
            ->where('is_active', true)
            ->get();

        foreach ($products as $product) {
            $this->warn("Produto '{$product->name}' vence em {$product->expiration_date->format('d/m/Y')}");
            $count++;
        }

        $batches = StockMovement::whereNotNull('expiry_date')
            ->where('expiry_date', '<=', $threshold)
            ->where('type', 'in')
            ->with('product')
            ->get();

        foreach ($batches as $movement) {
            $pname = $movement->product->name ?? 'N/A';
            $this->warn("Lote '{$movement->lot_number}' de '{$pname}' vence em {$movement->expiry_date->format('d/m/Y')}");
            $count++;
        }

        $this->info("{$count} produto(s)/lote(s) próximos ao vencimento.");
        return 0;
    }
}
