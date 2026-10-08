<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BranchScoped;

class MedicationReservation extends Model
{
    use HasFactory, BranchScoped;

    protected $fillable = [
        'product_id', 'branch_id', 'pet_id', 'requested_by', 'fulfilled_by',
        'quantity', 'unit', 'priority', 'status', 'source_type', 'source_id',
        'notes', 'fulfilled_at', 'cancelled_at',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'fulfilled_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
    public function pet(): BelongsTo { return $this->belongsTo(Pet::class); }
    public function requestedBy(): BelongsTo { return $this->belongsTo(User::class, 'requested_by'); }
    public function fulfilledBy(): BelongsTo { return $this->belongsTo(User::class, 'fulfilled_by'); }
}
