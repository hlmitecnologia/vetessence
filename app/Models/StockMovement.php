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
        'product_id', 'type', 'quantity', 'batch_number', 'lot_number',
        'expiry_date', 'balance_after', 'reference', 'notes',
        'user_id', 'created_at', 'branch_id', 'movement_reason',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'balance_after' => 'integer',
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
