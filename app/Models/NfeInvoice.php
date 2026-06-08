<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NfeInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id', 'invoice_id',
        'nfe_number', 'nfe_key', 'status',
        'issuance_date', 'cancelled_at',
        'nfe_url_xml', 'nfe_url_pdf', 'danfe_url',
        'provider_response', 'error_message',
    ];

    protected $casts = [
        'issuance_date' => 'datetime',
        'cancelled_at' => 'datetime',
        'provider_response' => 'json',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
