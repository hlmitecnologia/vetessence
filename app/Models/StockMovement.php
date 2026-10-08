<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BranchScoped;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockMovement extends Model
{
    use HasFactory, BranchScoped;

    public $timestamps = true;

    protected $fillable = [
        'product_id', 'inventory_batch_id', 'inventory_container_id', 'type', 'quantity', 'unit', 'batch_number', 'lot_number',
        'expiry_date', 'balance_after', 'reference', 'notes',
        'user_id', 'created_at', 'branch_id', 'movement_reason', 'idempotency_key',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'balance_after' => 'decimal:4',
        'expiry_date' => 'date',
        'created_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
