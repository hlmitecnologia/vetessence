<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BranchScoped;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthCertificate extends Model
{
    use HasFactory, BranchScoped;

    protected $fillable = [
        'certificate_number', 'pet_id', 'type', 'destination',
        'issuer_vet_id', 'issue_date', 'expiration_date',
        'clinical_notes', 'is_export', 'status',
        'pdf_generated_at', 'notes',
        'cvi_number', 'destination_country', 'transport_mode',
        'embarkation_date', 'crmv_emitter', 'requirements_checklist',
        'is_cvi', 'approved_by', 'branch_id',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiration_date' => 'date',
        'is_export' => 'boolean',
        'is_cvi' => 'boolean',
        'pdf_generated_at' => 'datetime',
        'requirements_checklist' => 'array',
        'embarkation_date' => 'date',
    ];

    public function pet(): BelongsTo { return $this->belongsTo(Pet::class); }
    public function issuerVet(): BelongsTo { return $this->belongsTo(User::class, 'issuer_vet_id'); }
    public function approvedBy(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }

    public static function generateNumber(): string
    {
        $year = now()->year;
        $last = static::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $seq = $last ? (int) substr($last->certificate_number, 3, 4) + 1 : 1;
        return sprintf('HC-%04d/%d', $seq, $year);
    }
}
