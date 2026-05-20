<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NfseInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'invoice_id',
        'nfse_number',
        'nfse_code',
        'nfse_url_xml',
        'nfse_url_pdf',
        'rps_number',
        'status',
        'issuance_date',
        'cancelled_at',
        'verification_code',
        'provider_response',
        'error_message',
    ];

    protected $casts = [
        'issuance_date' => 'datetime',
        'cancelled_at' => 'datetime',
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
