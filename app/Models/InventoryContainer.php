<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryContainer extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_batch_id', 'identifier', 'initial_quantity', 'available_quantity',
        'unit', 'status', 'opened_at', 'reconstituted_at', 'beyond_use_at', 'notes',
    ];

    protected $casts = [
        'initial_quantity' => 'decimal:4',
        'available_quantity' => 'decimal:4',
        'opened_at' => 'datetime',
        'reconstituted_at' => 'datetime',
        'beyond_use_at' => 'datetime',
    ];

    public function batch(): BelongsTo { return $this->belongsTo(InventoryBatch::class, 'inventory_batch_id'); }

    public function isUsable(): bool
    {
        return in_array($this->status, ['unopened', 'opened'], true)
            && ($this->beyond_use_at === null || $this->beyond_use_at->isFuture())
            && (float) $this->available_quantity > 0;
    }
}
