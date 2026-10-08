<?php

namespace App\Console\Commands;

use App\Models\InventoryBatch;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReconcileLegacyInventory extends Command
{
    protected $signature = 'inventory:reconcile-legacy
        {--apply : Persist the reconciliation; without this option the command is a report only}
        {--branch= : Branch ID for the legacy balance}
        {--confirm-file= : CSV with product_id,branch_id,quantity,unit,expiration_date}
        {--json : Print machine-readable JSON}';

    protected $description = 'Report or reconcile products with legacy stock and no inventory batch';

    public function handle(): int
    {
        $branchId = $this->option('branch') !== null ? (int) $this->option('branch') : null;
        $rows = $this->loadRows($branchId);

        if ($rows === null) {
            return self::FAILURE;
        }

        $results = [];
        foreach ($rows as $row) {
            $product = Product::find($row['product_id']);
            if (!$product) {
                $results[] = ['status' => 'invalid', 'reason' => 'product_not_found'] + $row;
                continue;
            }

            $branch = ($row['branch_id'] ?? $branchId) ?: null;
            $quantity = (float) $row['quantity'];
            $unit = $row['unit'] ?: ($product->stock_unit ?: $product->unit ?: 'un');
            $expiration = $row['expiration_date'] ?: null;
            $confirmed = $row['confirmed'] ?? false;
            $status = $confirmed && $quantity > 0 && $expiration ? 'active' : 'quarantined';

            $existing = InventoryBatch::withoutGlobalScopes()
                ->where('product_id', $product->id)
                ->where('branch_id', $branch)
                ->where('is_legacy', true)
                ->first();

            $result = [
                'product_id' => $product->id,
                'product' => $product->name,
                'branch_id' => $branch,
                'quantity' => $quantity,
                'unit' => $unit,
                'expiration_date' => $expiration,
                'status' => $existing ? 'exists' : $status,
                'reason' => $confirmed ? 'physical_reconciliation' : 'unconfirmed_legacy_balance',
            ];

            if ($this->option('apply') && !$existing && $quantity > 0) {
                $batch = DB::transaction(function () use ($product, $branch, $quantity, $unit, $expiration, $status, $confirmed) {
                    return InventoryBatch::withoutGlobalScopes()->create([
                        'product_id' => $product->id,
                        'branch_id' => $branch,
                        'batch_number' => 'LEGACY-SIN-LOTE',
                        'lot_number' => 'LEGACY-SIN-LOTE',
                        'expiration_date' => $expiration,
                        'quantity_received' => $quantity,
                        'quantity_available' => $quantity,
                        'unit' => $unit,
                        'package_quantity' => $product->package_quantity ?: 1,
                        'package_count' => $product->package_quantity > 0 ? $quantity / $product->package_quantity : $quantity,
                        'unit_cost' => null,
                        'status' => $status,
                        'is_legacy' => true,
                        'notes' => $confirmed
                            ? 'Criado por reconciliação física de estoque legado.'
                            : 'Saldo legado não confirmado; mantido em quarentena até conferência e autorização.',
                    ]);
                });
                $result['status'] = $batch->status;
                $result['batch_id'] = $batch->id;
            }

            $results[] = $result;
        }

        if ($this->option('json')) {
            $this->line(json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            $this->table(['Produto', 'Filial', 'Quantidade', 'Unidade', 'Validade', 'Status'], array_map(
                fn (array $row) => [
                    $row['product'] ?? $row['product_id'],
                    $row['branch_id'] ?? '-',
                    $row['quantity'] ?? '-',
                    $row['unit'] ?? '-',
                    $row['expiration_date'] ?? 'desconhecida',
                    $row['status'] ?? 'invalid',
                ],
                $results
            ));
        }

        if (!$this->option('json')) {
            $this->info(sprintf('%d saldo(s) analisado(s). %s', count($results), $this->option('apply') ? 'Alterações aplicadas.' : 'Relatório; use --apply para persistir.'));
        }
        return self::SUCCESS;
    }

    private function loadRows(?int $branchId): ?array
    {
        $file = $this->option('confirm-file');
        if ($file) {
            if (!is_readable($file)) {
                $this->error("Arquivo de reconciliação não encontrado ou ilegível: {$file}");
                return null;
            }
            $handle = fopen($file, 'rb');
            $headers = fgetcsv($handle, 0, ',', '"', '');
            if (!$headers || array_diff(['product_id', 'branch_id', 'quantity', 'unit', 'expiration_date'], $headers)) {
                fclose($handle);
                $this->error('CSV deve conter: product_id,branch_id,quantity,unit,expiration_date');
                return null;
            }
            $rows = [];
            while (($values = fgetcsv($handle, 0, ',', '"', '')) !== false) {
                $row = array_combine($headers, $values);
                $row['confirmed'] = true;
                $rows[] = $row;
            }
            fclose($handle);
            return $rows;
        }

        return Product::query()
            ->where('stock', '>', 0)
            ->whereDoesntHave('movements', fn ($query) => $query->whereNotNull('inventory_batch_id'))
            ->get(['id', 'stock'])
            ->map(fn (Product $product) => [
                'product_id' => $product->id,
                'branch_id' => $branchId,
                'quantity' => (float) $product->stock,
                'unit' => null,
                'expiration_date' => null,
                'confirmed' => false,
            ])
            ->all();
    }
}
