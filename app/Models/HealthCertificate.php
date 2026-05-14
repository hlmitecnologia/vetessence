<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'certificate_number', 'pet_id', 'type', 'destination',
        'issuer_vet_id', 'issue_date', 'expiration_date',
        'clinical_notes', 'is_export', 'status',
        'pdf_generated_at', 'notes',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiration_date' => 'date',
        'is_export' => 'boolean',
        'pdf_generated_at' => 'datetime',
    ];

    public function pet(): BelongsTo { return $this->belongsTo(Pet::class); }
    public function issuerVet(): BelongsTo { return $this->belongsTo(User::class, 'issuer_vet_id'); }

    public static function generateNumber(): string
    {
        $year = now()->year;
        $last = static::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $seq = $last ? (int) substr($last->certificate_number, 3, 4) + 1 : 1;
        return sprintf('HC-%04d/%d', $seq, $year);
    }
}
