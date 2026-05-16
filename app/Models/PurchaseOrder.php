<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\BranchScoped;

class PurchaseOrder extends Model
{
    use HasFactory, BranchScoped;

    protected $fillable = [
        'order_number', 'supplier_id', 'branch_id', 'status',
        'requested_by', 'approved_by', 'total', 'notes',
        'ordered_at', 'approved_at', 'received_at',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'ordered_at' => 'datetime',
        'approved_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }
    public function requester(): BelongsTo { return $this->belongsTo(User::class, 'requested_by'); }
    public function approver(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
    public function items(): HasMany { return $this->hasMany(PurchaseOrderItem::class); }

    public static function generateNumber(): string
    {
        $year = now()->year;
        $last = static::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $seq = $last ? (int) substr($last->order_number, 3, 4) + 1 : 1;
        return sprintf('PO-%04d/%d', $seq, $year);
    }

    public function scopeDraft($q) { return $q->where('status', 'draft'); }
    public function scopeOrdered($q) { return $q->where('status', 'ordered'); }
    public function scopePending($q) { return $q->whereIn('status', ['draft', 'ordered']); }
}
