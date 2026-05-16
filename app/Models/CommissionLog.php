<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'invoice_id', 'commission_rate_id', 'description',
        'base_value', 'commission_value', 'status', 'paid_at',
    ];

    protected $casts = [
        'base_value' => 'decimal:2',
        'commission_value' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function commissionRate(): BelongsTo
    {
        return $this->belongsTo(CommissionRate::class);
    }

    public function scopePending($q) { return $q->where('status', 'pending'); }
    public function scopePaid($q) { return $q->where('status', 'paid'); }
}
