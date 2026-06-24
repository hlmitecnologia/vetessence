<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StockForecastService
{
    public function calculateAvgDailyConsumption(Product $product, int $days = 30): float
    {
        $since = now()->subDays($days);
        $exits = StockMovement::where('product_id', $product->id)
            ->whereIn('type', ['exit', 'loss', 'transfer_out'])
            ->where('created_at', '>=', $since)
            ->sum('quantity');

        return $days > 0 ? round($exits / $days, 4) : 0;
    }

    public function updateReorderPoint(Product $product): void
    {
        $avgDaily = $this->calculateAvgDailyConsumption($product);
        $leadTime = $product->supplier?->lead_time_days ?? 0;
        $safetyStock = $product->safety_stock ?: 0;
        $reorderPoint = ($avgDaily * $leadTime) + $safetyStock;

        $product->update([
            'avg_daily_consumption' => $avgDaily,
            'reorder_point' => round($reorderPoint, 2),
            'last_consumption_calculated_at' => now(),
        ]);
    }

    public function suggestPurchaseOrder(?Branch $branch = null): Collection
    {
        $query = Product::with('supplier')->needingReorder()->where('is_active', true);

        if ($branch) {
            $query->whereHas('movements', fn ($q) => $q->where('branch_id', $branch->id));
        }

        return $query->get()->map(function (Product $product) {
            $leadTime = $product->supplier?->lead_time_days ?? 0;
            $suggestedQty = max(0, ceil($product->reorder_point - $product->stock));

            return (object) [
                'product' => $product,
                'supplier' => $product->supplier,
                'current_stock' => $product->stock,
                'avg_daily_consumption' => $product->avg_daily_consumption,
                'reorder_point' => $product->reorder_point,
                'lead_time_days' => $leadTime,
                'suggested_quantity' => $suggestedQty,
            ];
        })->filter(fn ($item) => $item->suggested_quantity > 0)
          ->sortByDesc(fn ($item) => $item->suggested_quantity)
          ->values();
    }

    public function expiringProducts(int $days = 30, ?Branch $branch = null): Collection
    {
        $query = Product::with('supplier')->expiringSoon($days)->where('is_active', true);

        if ($branch) {
            $query->whereHas('movements', fn ($q) => $q->where('branch_id', $branch->id));
        }

        return $query->get()->map(function (Product $product) {
            $daysToExpiry = now()->diffInDays($product->expiration_date, false);
            $totalValue = $product->stock * $product->cost_price;

            return (object) [
                'product' => $product,
                'days_to_expiry' => (int) $daysToExpiry,
                'total_value' => $totalValue,
                'batch_number' => $product->batch_number,
            ];
        })->sortBy('days_to_expiry')->values();
    }

    public function recalculateAll(): array
    {
        $count = 0;
        Product::where('is_active', true)->chunk(100, function ($products) use (&$count) {
            foreach ($products as $product) {
                $this->updateReorderPoint($product);
                $count++;
            }
        });
        return ['updated' => $count];
    }
}
