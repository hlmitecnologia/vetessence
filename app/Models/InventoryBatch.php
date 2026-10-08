<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\BranchScoped;

class InventoryBatch extends Model
{
    use HasFactory, BranchScoped;

    protected $fillable = [
        'product_id', 'branch_id', 'batch_number', 'lot_number', 'expiration_date',
        'quantity_received', 'quantity_available', 'unit', 'package_quantity',
        'package_count', 'unit_cost', 'status', 'is_legacy', 'notes',
    ];

    protected $casts = [
        'expiration_date' => 'date',
        'quantity_received' => 'decimal:4',
        'quantity_available' => 'decimal:4',
        'package_quantity' => 'decimal:4',
        'package_count' => 'decimal:4',
        'unit_cost' => 'decimal:4',
        'is_legacy' => 'boolean',
    ];

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
    public function containers(): HasMany { return $this->hasMany(InventoryContainer::class); }

    public function isExpired(): bool
    {
        return $this->expiration_date?->isPast() ?? false;
    }
}
