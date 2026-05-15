<?php

namespace App\Console\Commands;

use App\Models\InventoryReconciliation;
use App\Models\Product;
use Illuminate\Console\Command;

class AlertaEstoque extends Command
{
    protected $signature = 'inventory:reconcile
        {--product= : Specific product ID to reconcile}
        {--threshold=5 : Variance threshold to flag alerts}';

    protected $description = 'Check inventory reconciliation for significant variances and flag alerts';

    public function handle()
    {
        $threshold = (float) $this->option('threshold');
        $query = InventoryReconciliation::where('status', 'pending')
            ->whereRaw('ABS(variance) > ?', [$threshold]);

        if ($productId = $this->option('product')) {
            $query->where('product_id', $productId);
        }

        $alerts = $query->get();

        if ($alerts->isEmpty()) {
            $this->info('Nenhuma variação significativa encontrada.');
            return 0;
        }

        foreach ($alerts as $alert) {
            $product = $alert->product;
            $direction = $alert->variance > 0 ? 'excesso' : 'falta';
            $this->warn("Produto '{$product->name}' (SKU: {$product->sku}): {$direction} de " . abs($alert->variance) . " unidades.");
        }

        $this->info("Total de {$alerts->count()} alerta(s) de variação de estoque.");

        return 1;
    }
}
