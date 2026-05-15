<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BranchScoped;

class InventoryReconciliation extends Model
{
    use HasFactory, BranchScoped;

    protected $fillable = [
        'product_id', 'user_id', 'expected_quantity', 'actual_quantity',
        'variance', 'type', 'notes', 'status', 'reconciled_at',
        'approved_by', 'branch_id',
    ];

    protected $casts = [
        'reconciled_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
