<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConvenioClaim extends Model
{
    use HasFactory;

    protected $fillable = [
        'convenio_pet_id', 'invoice_id', 'claim_number', 'status',
        'amount_requested', 'amount_approved', 'notes', 'filed_at', 'response_at',
    ];

    protected $casts = [
        'amount_requested' => 'decimal:2',
        'amount_approved' => 'decimal:2',
        'filed_at' => 'datetime',
        'response_at' => 'datetime',
    ];

    public function convenioPet(): BelongsTo
    {
        return $this->belongsTo(ConvenioPet::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
