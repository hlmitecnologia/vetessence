<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_account_id', 'external_id', 'description', 'amount',
        'transaction_date', 'type', 'status', 'notes', 'invoice_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function scopePending($q) { return $q->where('status', 'pending'); }
    public function scopeReconciled($q) { return $q->where('status', 'reconciled'); }
    public function scopeUnmatched($q) { return $q->where('status', 'unmatched'); }
}
