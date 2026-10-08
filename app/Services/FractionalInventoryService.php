<?php

namespace App\Services;

use App\Models\InventoryBatch;
use App\Models\InventoryContainer;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use InvalidArgumentException;

class FractionalInventoryService
{
    public function consume(Product $product, float $quantity, string $unit, array $options = []): StockMovement
    {
        if (!$product->allows_fractional) {
            throw new InvalidArgumentException('O medicamento não está marcado como fracionável.');
        }
        if ($quantity <= 0) {
            throw new InvalidArgumentException('A quantidade consumida deve ser maior que zero.');
        }
        $this->assertUnit($product, $unit);

        return DB::transaction(function () use ($product, $quantity, $unit, $options) {
            if (!empty($options['idempotency_key'])) {
                $existing = StockMovement::where('idempotency_key', $options['idempotency_key'])->first();
                if ($existing) {
                    return $existing;
                }
            }

            $batches = InventoryBatch::query()
                ->where('product_id', $product->id)
                ->where('status', 'active')
                ->where('unit', $unit)
                ->when($options['batch_id'] ?? null, fn ($q, $id) => $q->whereKey($id))
                ->where('quantity_available', '>', 0)
                ->orderByRaw('expiration_date IS NULL')
                ->orderBy('expiration_date')
                ->lockForUpdate()
                ->get();

            $remaining = $quantity;
            $lastMovement = null;
            foreach ($batches as $batch) {
                if ($batch->isExpired()) {
                    continue;
                }

                $containers = collect([null]);
                if ($product->requires_container_tracking) {
                    $containers = $batch->containers()
                        ->whereIn('status', ['unopened', 'opened'])
                        ->where('available_quantity', '>', 0)
                        ->orderByRaw('beyond_use_at IS NULL')
                        ->orderBy('beyond_use_at')
                        ->lockForUpdate()
                        ->get();
                }

                foreach ($containers as $container) {
                    if ($container instanceof InventoryContainer && !$container->isUsable()) {
                        continue;
                    }
                    $available = (float) ($container?->available_quantity ?? $batch->quantity_available);
                    $used = min($remaining, $available);
                    if ($used <= 0) {
                        continue;
                    }

                    if ($container) {
                        $this->openContainerIfNeeded($product, $container);
                        $container->available_quantity = $available - $used;
                        $container->status = (float) $container->available_quantity <= 0 ? 'empty' : 'opened';
                        $container->save();
                    }

                    $batch->quantity_available = (float) $batch->quantity_available - $used;
                    $batch->save();
                    $product->decrement('stock', $used);

                    $movementData = [
                        'product_id' => $product->id,
                        'inventory_batch_id' => $batch->id,
                        'inventory_container_id' => $container?->id,
                        'type' => 'out',
                        'quantity' => $used,
                        'unit' => $unit,
                        'balance_after' => $batch->quantity_available,
                        'reference' => $options['reference'] ?? 'fractional-consumption',
                        'notes' => $options['notes'] ?? null,
                        'branch_id' => $batch->branch_id,
                        'movement_reason' => $options['reason'] ?? 'consumption',
                        'idempotency_key' => $options['idempotency_key'] ?? null,
                    ];
                    if (($options['user_id'] ?? auth()->id()) !== null) {
                        $movementData['user_id'] = $options['user_id'] ?? auth()->id();
                    }
                    $lastMovement = StockMovement::create($movementData);
                    $remaining -= $used;
                    if ($remaining <= 0.00001) {
                        return $lastMovement;
                    }
                }
            }

            throw new RuntimeException(sprintf('Estoque insuficiente para %s %s.', $quantity, $unit));
        });
    }

    public function return(Product $product, InventoryBatch $batch, float $quantity, string $unit, ?InventoryContainer $container = null, array $options = []): StockMovement
    {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('A quantidade devolvida deve ser maior que zero.');
        }
        $this->assertUnit($product, $unit);

        return DB::transaction(function () use ($product, $batch, $quantity, $unit, $container, $options) {
            if ($container) {
                $container->available_quantity = (float) $container->available_quantity + $quantity;
                $container->status = 'opened';
                $container->save();
            }
            $batch->quantity_available = (float) $batch->quantity_available + $quantity;
            $batch->save();
            $product->increment('stock', $quantity);

            $movementData = [
                'product_id' => $product->id,
                'inventory_batch_id' => $batch->id,
                'inventory_container_id' => $container?->id,
                'type' => 'in',
                'quantity' => $quantity,
                'unit' => $unit,
                'balance_after' => $batch->quantity_available,
                'reference' => $options['reference'] ?? 'fractional-return',
                'notes' => $options['notes'] ?? null,
                'branch_id' => $batch->branch_id,
                'movement_reason' => 'return',
                'idempotency_key' => $options['idempotency_key'] ?? null,
            ];
            if (($options['user_id'] ?? auth()->id()) !== null) {
                $movementData['user_id'] = $options['user_id'] ?? auth()->id();
            }
            return StockMovement::create($movementData);
        });
    }

    private function assertUnit(Product $product, string $unit): void
    {
        $allowed = array_filter([$product->stock_unit, $product->dispensing_unit]);
        if ($allowed && !in_array($unit, $allowed, true)) {
            throw new InvalidArgumentException('A unidade informada não corresponde ao cadastro do medicamento.');
        }
    }

    private function openContainerIfNeeded(Product $product, InventoryContainer $container): void
    {
        if ($container->status !== 'unopened') {
            return;
        }
        $days = $product->opened_use_days ?? 5;
        $container->opened_at = now();
        $container->beyond_use_at = now()->addDays($days);
    }
}
