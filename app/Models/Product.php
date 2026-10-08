<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'supplier_id', 'sku', 'barcode', 'batch_number',
        'lot_number', 'name', 'description', 'unit', 'cost_price',
        'sale_price', 'fractional_cost_price', 'fractional_sale_price', 'stock', 'min_stock', 'max_stock',
        'stock_unit', 'dispensing_unit', 'package_quantity', 'allows_fractional',
        'requires_container_tracking', 'opened_use_days', 'reconstituted_use_days',
        'expiration_date', 'is_active',
        'ncm', 'cest', 'cfop', 'cst', 'csosn', 'ibpt_percentage',
        'weight_kg', 'icms_origin', 'icms_cst', 'icms_modbc',
        'icms_vbc', 'icms_picms', 'icms_predbc',
        'ipi_cst', 'ipi_aliquot',
        'pis_cst', 'cofins_cst', 'pis_aliquot', 'cofins_aliquot',
        'fiscal_classification',
        'avg_daily_consumption', 'safety_stock', 'reorder_point', 'last_consumption_calculated_at',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'fractional_cost_price' => 'decimal:4',
        'fractional_sale_price' => 'decimal:4',
        'package_quantity' => 'decimal:4',
        'stock' => 'decimal:4',
        'allows_fractional' => 'boolean',
        'requires_container_tracking' => 'boolean',
        'opened_use_days' => 'integer',
        'reconstituted_use_days' => 'integer',
        'expiration_date' => 'date',
        'is_active' => 'boolean',
        'ibpt_percentage' => 'decimal:2',
        'weight_kg' => 'decimal:3',
        'icms_vbc' => 'decimal:2',
        'icms_picms' => 'decimal:2',
        'icms_predbc' => 'decimal:2',
        'ipi_aliquot' => 'decimal:2',
        'pis_aliquot' => 'decimal:2',
        'cofins_aliquot' => 'decimal:2',
        'avg_daily_consumption' => 'decimal:4',
        'safety_stock' => 'decimal:2',
        'reorder_point' => 'decimal:2',
        'last_consumption_calculated_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->stock <= $this->min_stock;
    }

    public function getIsBelowReorderPointAttribute(): bool
    {
        return $this->reorder_point > 0 && $this->stock < $this->reorder_point;
    }

    public function scopeNeedingReorder($query)
    {
        return $query->where('reorder_point', '>', 0)
            ->whereColumn('stock', '<', 'reorder_point')
            ->where('is_active', true);
    }

    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->whereNotNull('expiration_date')
            ->whereDate('expiration_date', '<=', now()->addDays($days))
            ->whereDate('expiration_date', '>', now())
            ->where('is_active', true);
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiration_date')
            ->whereDate('expiration_date', '<', now())
            ->where('is_active', true);
    }

    public function getMarginAttribute(): float
    {
        if ($this->cost_price == 0) return 0;
        return (($this->sale_price - $this->cost_price) / $this->cost_price) * 100;
    }
}
