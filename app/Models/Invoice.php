<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BranchScoped;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Invoice extends Model
{
    use HasFactory, BranchScoped;

    protected $fillable = [
        'invoice_number', 'tutor_id', 'pet_id',
        'subtotal', 'discount', 'total', 'status', 'due_date',
        'paid_at', 'payment_method', 'payment_proof', 'pix_code',
        'pix_expiration', 'convenio_discount', 'notes', 'user_id', 'branch_id',
        'nfse_status', 'nfse_invoice_id', 'nfe_status', 'nfe_invoice_id', 'medical_record_id',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'pix_expiration' => 'datetime',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class);
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function appointments(): BelongsToMany
    {
        return $this->belongsToMany(Appointment::class, 'appointment_invoice')
            ->withTimestamps();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function nfseInvoice()
    {
        return $this->belongsTo(NfseInvoice::class);
    }

    public function nfeInvoice()
    {
        return $this->belongsTo(NfeInvoice::class);
    }

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public static function generateNumber(): string
    {
        $prefix = 'FAT';
        $year = date('Y');
        $last = self::whereYear('created_at', $year)->max('invoice_number');
        $sequence = $last ? (int) substr($last, -6) + 1 : 1;
        return sprintf('%s-%s-%06d', $prefix, $year, $sequence);
    }

    public function generatePixCode(): array
    {
        if (!$this->pix_code) {
            $txid = str_replace(['-', ' '], '', $this->invoice_number);
            $pixService = app(\App\Services\PixService::class);
            $qrcode = $pixService->generateQRCode((float) $this->total, $txid);
            
            $this->pix_code = $qrcode['payload'];
            $this->pix_expiration = now()->addDays(7);
            $this->save();
            
            return $qrcode;
        }
        
        $pixService = app(\App\Services\PixService::class);
        return [
            'payload' => $this->pix_code,
            'qrcode_base64' => $this->getQRCodeBase64(),
        ];
    }

    public function getQRCodeBase64(): string
    {
        if (empty($this->pix_code)) {
            return '';
        }
        
        $pixService = app(\App\Services\PixService::class);
        $result = $pixService->generateQRCode(0, '');
        return $result['qrcode_base64'];
    }

    public function isPixExpired(): bool
    {
        return $this->pix_expiration && $this->pix_expiration->isPast();
    }
}
